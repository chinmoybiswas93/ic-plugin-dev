jQuery(document).ready(function ($) {
  $responseContainer = $("#secure-plugin-form-response");
  //handle form submission
  $("#secure-plugin-form").on("submit", function (e) {
    e.preventDefault();
    console.log("Form submitted");
    //ajax form submission
    $.ajax({
      url: securePlugin.ajax_url,
      type: "POST",
      data: {
        action: "secure_plugin_form_action",
        nonce: securePlugin.nonce,
        formData: $(this).serialize(),
      },
      success: function (response) {
        if (response.success) {
          $responseContainer.html(
            "<h3>Form Submitted Successfully</h3>" +
              "<p>Name: " +
              $("<div>").text(response.data.name).html() +
              "</p>" +
              "<p>Email: " +
              $("<div>").text(response.data.email).html() +
              "</p>" +
              "<p>Age: " +
              $("<div>").text(response.data.age).html() +
              "</p>" +
              "<p>Message: " +
              $("<div>").text(response.data.message).html() +
              "</p>"
          );
        } else {
          $responseContainer.html(
            '<div class="alert alert-danger"> <strong>Error:</strong> ' +
              response.data +
              "</div>"
          );
        }
      },
    });
  });
});
