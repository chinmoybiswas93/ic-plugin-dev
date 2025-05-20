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
        $responseContainer.html(
          "<h3>Form Submitted Successfully</h3>" +
            "<p>Name: " +
            $("<div>").text(response.name).html() +
            "</p>" +
            "<p>Email: " +
            $("<div>").text(response.email).html() +
            "</p>" +
            "<p>Age: " +
            $("<div>").text(response.age).html() +
            "</p>" +
            "<p>Message: " +
            $("<div>").text(response.message).html() +
            "</p>"
        );
      },
    });
  });
});
