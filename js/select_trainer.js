(function ($) {
  $(document).ready(function () {
    $("a.select-trainer").live("click", function (e) {
      e.preventDefault();
      $("div.courses_popular").each(function() {
        $(this).removeClass('select-border');
      });
      var id = $(this).parent().attr("data-id");
      $(this).parent().addClass("select-border");
      $("input[name=selected_trainer_id]").val(id);
    });
    
  })
})(jQuery);
  