jQuery(document).ready(function ($) {
  console.log("Admin Panel script loaded");

  const form = $("#quick-qr-code-settings-form");

  // Generate QR code preview
  function generateQrCodePreview() {
    const label = $("#qr-code-label").val() || "Scan Me";
    const size = $("#qr-code-size").val() || "150";
    const margin = $("#qr-code-margin").val() || "2";
    const format = "png";
    const colorDark = $("#qr-code-dark").val() || "000000";
    const colorLight = $("#qr-code-light").val() || "ffffff";
    const logourl = $("#qr-code-logo-url").val() || "";
    const logoSize = $("#qr-code-logo-size").val() || "50";

    const qrurl = "https://quickchart.io/qr?";
    const params = {
      text: QuickQRCodeAjax.siteUrl,
      size: size,
      margin: margin,
      format: format,
      dark: colorDark,
      light: colorLight,
    };
    if (logourl) {
      params.centerImageUrl = logourl;
      params.centerImageSizeRatio = logoSize / 100; // Convert to ratio
    }
    const queryString = new URLSearchParams(params).toString();
    const qrCodeUrl = qrurl + queryString;
    const qrCodeImage = `<img src="${qrCodeUrl}" alt="QR Code Preview" style="max-width: 100%; height: auto;">`;
    const previewContainer = $("#qr-code-preview");
    previewContainer.html(`<h3>${label}</h3>` + qrCodeImage);
  }

  // Form submission via AJAX
  form.on("submit", function (e) {
    e.preventDefault();
    const formData =
      form.serialize() +
      "&action=quick_qr_code_save_settings" +
      "&security=" +
      QuickQRCodeAjax.nonce;

    // Clear previous notices
    $(".notice").remove();

    $.ajax({
      url: QuickQRCodeAjax.ajax_url,
      type: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        const dismissBtn =
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
          const errors = response.data.errors || ["An error occurred."];
          let errorHtml =
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
      error: function (xhr) {
        const dismissBtn =
          '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>';
        let errorMsg = "An unexpected error occurred.";
        if (xhr.status === 403) {
          errorMsg =
            "Security check failed (invalid or expired nonce). Please reload the page and try again.";
        }
        form.before(
          '<div class="notice notice-error is-dismissible" style="margin-top:20px;">' +
            dismissBtn +
            "<ul><li>" +
            errorMsg +
            "</li></ul></div>"
        );
      },
    });
  });

  // Dismiss notice handler (bind once)
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

  // Update preview on input change
  form.on("change input", "input, select", function () {
    generateQrCodePreview();
  });

  // Initial preview
  generateQrCodePreview();
});
