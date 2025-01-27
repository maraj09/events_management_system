//////////////////////////////////////
// Create Event - Start
//////////////////////////////////////

let currentPage = 1;

function loadEvents(page = 1, perPage = 8) {
  $.ajax({
    url: "./inc/router.php",
    type: "GET",
    data: { page: page, perPage: perPage, action: "load-events" },
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
  loadEvents(page);
});

loadEvents();

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

  if (isValid) {
    const formData = new FormData(this);
    $.ajax({
      url: $(this).attr("action"),
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
          showAlert("success", "Event created successfully!");
          currentPage = 1;
          loadEvents(currentPage);
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
          <h5 class="card-title">${event.name}</h5>
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
        <div class="card-footer d-flex">
          <button class="btn btn-sm btn-warning edit-event ms-auto" data-id="${
            event.id
          }" data-bs-toggle="modal" data-bs-target="#eventModal">Edit</button>
          <button class="btn btn-sm btn-outline-danger delete-event ms-2" data-id="${
            event.id
          }">Delete</button>
        </div>
      </div>
    </div>
  `;
}

//////////////////////////////////////
// Create Event - End
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
