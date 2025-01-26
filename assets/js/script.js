//////////////////////////////////////
// Create Event - Start
//////////////////////////////////////

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

//////////////////////////////////////
// Create Event - End
//////////////////////////////////////

//////////////////////////////////////
// Bootstrap Alert - Start
//////////////////////////////////////
function showAlert(type, message) {
  // Create a Bootstrap alert element
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
