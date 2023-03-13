(function ($) {
  $(document).ready(function () {
    $("#dialog").hide();

    $("a#request_client").colorbox({ iframe: true, width: 600, height: 600 });

    $(".confirmDeleteLink").click(function (e) {
      e.preventDefault();
      var targetUrl = $(this).attr("href");

      $("#dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons: {
          "Yes, I am not longer training this client": function () {
            window.location.href = targetUrl;
          },
          Cancel: function () {
            $(this).dialog("close");
          },
        },
      });

      $("#dialog").dialog("open");
    });
  });
})(jQuery);
