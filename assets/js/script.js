//////////////////////////////////////
// Event - Start
//////////////////////////////////////

let currentPage = 1;

function loadEvents(page = 1, perPage = 8) {
  const eventFilterValue = $("#eventFilter").val();
  const eventSearch = $("#eventSearch").val();
  $.ajax({
    url: "./inc/router.php",
    type: "GET",
    data: {
      page: page,
      perPage: perPage,
      action: "load-events",
      filter: eventFilterValue,
      search: eventSearch,
    },
    success: function (response) {
      response = JSON.parse(response);
      if (response.status === "success") {
        const events = response.data;
        const totalEvents = response.totalEvents;
        const perPage = response.perPage;
        const totalPages = Math.ceil(totalEvents / perPage);

        $(".event-container").empty();
        events.forEach((event) => {
          $(".event-container").append(generateEventCard(event));
        });

        generatePaginationLinks(totalPages, page);
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

$(document).on("change", "#eventFilter", function () {
  loadEvents();
});

$(document).on("keyup", "#eventSearch", function () {
  loadEvents();
});

function generatePaginationLinks(totalPages, currentPage) {
  const paginationContainer = $("#pagination");
  paginationContainer.empty();

  for (let i = 1; i <= totalPages; i++) {
    const activeClass = i === currentPage ? "active" : "";
    paginationContainer.append(`
      <li class="page-item ${activeClass}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>
    `);
  }
}

$(document).on("click", "#pagination .page-link", function (e) {
  e.preventDefault();
  const page = $(this).data("page");
  currentPage = page;
  loadEvents(page);
});

loadEvents();

$("#new-event").on("click", function () {
  $("#eventForm")[0].reset();
  $(".form-control").removeClass("is-invalid");
  $("#eventModal").find("[data-id]").removeAttr("data-id");

  $("#eventForm [name='action']").val("add-event");
  $("#eventModal [type='submit']").text("Create Event");
});

$("#eventForm").on("submit", function (e) {
  e.preventDefault();

  let isValid = true;

  const allowedImageTypes = [
    "image/jpeg",
    "image/png",
    "image/jpg",
    "image/gif",
  ];

  $(".form-control").removeClass("is-invalid");

  const eventName = $("#eventName").val().trim();
  if (!eventName) {
    $("#eventName").addClass("is-invalid");
    isValid = false;
  }

  const eventDescription = $("#eventDescription").val().trim();
  if (!eventDescription) {
    $("#eventDescription").addClass("is-invalid");
    isValid = false;
  }

  const eventImage = $("#eventImage")[0].files[0];
  if (eventImage && !allowedImageTypes.includes(eventImage.type)) {
    $("#eventImage").addClass("is-invalid");
    isValid = false;
  }

  const eventDate = $("#eventDate").val();
  if (!eventDate) {
    $("#eventDate").addClass("is-invalid");
    isValid = false;
  }

  const userLimit = $("#userLimit").val();
  if (!userLimit || userLimit <= 0) {
    $("#userLimit").addClass("is-invalid");
    isValid = false;
  }

  const eventId = $(this).attr("data-id");

  const actionUrl = eventId
    ? `./inc/router.php?id=${eventId}`
    : $(this).attr("action");

  if (isValid) {
    const formData = new FormData(this);
    $.ajax({
      url: actionUrl,
      type: $(this).attr("method"),
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        response = JSON.parse(response);
        if (response.status === "success") {
          $("#eventModal").modal("hide");
          $("#eventForm")[0].reset();
          $(".form-control").removeClass("is-invalid");
          if (eventId) {
            showAlert("success", "Event updated successfully!");
            loadEvents(currentPage);
          } else {
            showAlert("success", "Event created successfully!");
            currentPage = 1;
            loadEvents(currentPage);
          }
        } else {
          var errors = response.errors;
          $.each(errors, function (field, message) {
            var input = $("[name='" + field + "']");
            input.addClass("is-invalid");
            input.next(".invalid-feedback").remove();
            input.after("<div class='invalid-feedback'>" + message + "</div>");
            showAlert("danger", message);
          });
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  }
});

$(document).on("click", ".edit-event", function () {
  const eventId = $(this).data("id");

  $.ajax({
    url: `./inc/router.php`,
    type: "GET",
    data: { id: eventId, action: "edit-event" },
    success: function (response) {
      response = JSON.parse(response);

      if (response.status === "success") {
        $("#eventForm").attr("data-id", eventId);
        $("#eventForm [name='name']").val(response.event.name);
        $("#eventForm [name='action']").val("update-event");
        $("#eventForm [name='description']").val(response.event.description);
        $("#eventForm [name='event_date']").val(response.event.event_date);
        $("#eventForm [name='user_limit']").val(response.event.user_limit);
        $("#eventModal [type='submit']").text("Update Event");
      } else {
        showAlert("danger", "Failed to fetch event details!");
      }
    },
    error: function (error) {
      console.log(error);
      showAlert("danger", "An error occurred while fetching event details!");
    },
  });
});

function generateEventCard(event) {
  return `
    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
      <div class="card card-event h-100">
        <img src="${
          event.image
            ? "." + event.image
            : "./assets/images/event_placeholder.jpg"
        }" class="card-img-top" alt="Event Image">
        <div class="card-body">
          <a href="event.php?id=${
            event.id
          }" class="text-decoration-none"><h5 class="card-title">${
    event.name
  }</h5></a>
          <p class="card-text">${event.description.substring(0, 100)}${
    event.description.length > 100 ? "..." : ""
  }</p>
          <small>
            Event Date:
            <strong>${moment(event.event_date).format(
              "DD-MM-YYYY [at] hh:mm A"
            )}</strong>
          </small>
        </div>
        ${
          sessionUserId === event.user_id
            ? `<div class="card-footer d-flex">
            <small>
            Total Seat:
            <strong>${event.user_limit}</strong>
          </small>
          <button class="btn btn-sm btn-warning edit-event ms-auto" data-id="${event.id}" data-bs-toggle="modal" data-bs-target="#eventModal">Edit</button>
          <button class="btn btn-sm btn-outline-danger delete-event ms-2" data-id="${event.id}">Delete</button>
        </div>`
            : ""
        }
        
      </div>
    </div>
  `;
}

$(document).on("click", ".delete-event", function (e) {
  e.preventDefault();
  const eventId = $(this).data("id");

  $.ajax({
    url: "./inc/router.php",
    type: "POST",
    data: {
      action: "delete-event",
      id: eventId,
    },
    success: function (response) {
      response = JSON.parse(response);
      if (response.status === "success") {
        showAlert("success", "Event deleted successfully!");
        loadEvents(currentPage);
      } else {
        showAlert("error", response.message);
      }
    },
    error: function (error) {
      console.log(error);
      showAlert("error", "Something went wrong!");
    },
  });
});

//////////////////////////////////////
// Event - End
//////////////////////////////////////

//////////////////////////////////////
// Bootstrap Alert - Start
//////////////////////////////////////
function showAlert(type, message) {
  var alert = $(
    '<div class="alert alert-' +
      type +
      ' alert-dismissible fade show" role="alert">'
  ).text(message);

  $("#alert-container").html(alert);

  setTimeout(function () {
    alert.alert("close");
  }, 5000);
}

//////////////////////////////////////
// Bootstrap Alert - End
//////////////////////////////////////

//////////////////////////////////////
// Event Booking - Start
//////////////////////////////////////

$("#bookingForm").on("submit", function (e) {
  e.preventDefault();

  let quantity = $("#quantity").val();
  if (quantity < 1) {
    $("#quantity").addClass("is-invalid");
    return;
  }

  const formData = new FormData(this);
  console.log(formData);
  $.ajax({
    url: "./inc/router.php",
    type: "POST",
    data: formData,
    processData: false, 
    contentType: false,
    success: function (response) {
      response = JSON.parse(response);
      $("#bookingModal").modal("hide");
      $("#bookingForm")[0].reset();
      $("#quantity").removeClass("is-invalid");
      if (response.status === "success") {
        $("#availableSeatCount").text(response.availableSeat);
        showAlert("success", "Booking Successful!");
      } else {
        showAlert("danger", response.message);
      }
    },
    error: function () {
      alert("Something went wrong! Please try again.");
    },
  });
});

//////////////////////////////////////
// Event Booking - End
//////////////////////////////////////
