jQuery(document).ready(function ($) {
  $(".quick-qr-code").on("click", function () {
    const size = $(this).data("size");
    console.log("QR Code clicked! Size: " + size + " px");
  });
});


console.log("Quick QR Code script loaded.");