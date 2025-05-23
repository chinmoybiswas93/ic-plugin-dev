jQuery(document).ready(function ($) {
  console.log("Admin Panel script loaded");

  $("#quick-qr-code-settings-form").on("submit", function (e) {
    e.preventDefault();

    var form = $(this);
    var formData = form.serialize();
    formData += "&action=quick_qr_code_save_settings";
    formData += "&security=" + QuickQRCodeAjax.nonce;

    // Optionally, clear previous notices
    $(".notice").remove();

    $.ajax({
      url: QuickQRCodeAjax.ajax_url,
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        var dismissBtn =
          '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
        if (response.success) {
          form.before(
            '<div class="notice notice-success is-dismissible">' +
              dismissBtn +
              "<p>" +
              response.data.message +
              "</p></div>"
          );
        } else {
          var errors = response.data.errors || ["An error occurred."];
          var errorHtml =
            '<div class="notice notice-error is-dismissible" style="margin-top:20px;">' +
            dismissBtn +
            "<ul>";
          $.each(errors, function (i, err) {
            errorHtml += "<li>" + err + "</li>";
          });
          errorHtml += "</ul></div>";
          form.before(errorHtml);
        }
      },
    });

    // Delegate click event for dynamically added notices
    $(document).on(
      "click",
      ".notice.is-dismissible .notice-dismiss",
      function () {
        $(this)
          .closest(".notice")
          .fadeOut(200, function () {
            $(this).remove();
          });
      }
    );
  });
});
