(function ($) {
  $(document).ready(function () {
    $(".play-exercise").colorbox();
    $(".exercise_type").toggle();
    $(".section").toggle();
    $(".section_title").click(function () {
      $(this).removeClass("off").addClass("on");
      $(this).parent().find(".section").toggle();
      return false;
    });

    $(".type_title").click(function () {
      $(this).removeClass("off").addClass("on");
      $(this).parent().find(".exercise_type").toggle();
      return false;
    });

    $(".month").toggle();
    $(".month_title").click(function () {
      $(this).removeClass("off").addClass("on");
      $(this).parent().find(".month").toggle();
      return false;
    });

    $(".expand_all").click(function () {
      $(".section").toggle(true);
      $(".section_title").removeClass("off").addClass("on");
      $(".exercise_type").toggle(true);
      $(".type_title").removeClass("off").addClass("on");
      return false;
    });

    $("#client").change(function () {
      if ($("#client").val() != "") {
        window.location.href = "/member/client_log_book/" + $("#client").val();
      }
    });

    $(".confirmWorkoutDeleteLink").live("click", function (e) {
      e.preventDefault();
      var targetUrl = $(this).attr("href");

      $("#single_dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons: {
          "Yes, Please remove this workout": function () {
            $.post(
              "/member/remove_workout",
              { id: targetUrl },
              function (data) {
                if (data.success == "true") {
                  $("<h1>" + $("#log_date").html() + "</h1>").insertBefore(
                    "#logbook_container"
                  );
                  $("#logbook_container").html(
                    "<p>Your client does not have any workouts on this day</p>"
                  );
                }
                $("#single_dialog").dialog("close");
              },
              "json"
            );
          },
          Cancel: function () {
            $(this).dialog("close");
          },
        },
      });

      $("#single_dialog").dialog("open");
    });

    $(".confirmGroupWorkoutDeleteLink").live("click", function (e) {
      e.preventDefault();
      var targetUrl = $(this).attr("href");

      $("#group_single_dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons: {
          "Yes, Please remove this workout": function () {
            $.post(
              "/member/remove_group_workout",
              { id: targetUrl },
              function (data) {
                if (data.success == "true") {
                  $("<h1>" + $("#log_date").html() + "</h1>").insertBefore(
                    "#logbook_container"
                  );
                  $("#logbook_container").html(
                    "<p>Your client does not have any workouts on this day</p>"
                  );
                }
                $("#group_single_dialog").dialog("close");
              },
              "json"
            );
          },
          Cancel: function () {
            $(this).dialog("close");
          },
        },
      });

      $("#group_single_dialog").dialog("open");
    });

    $(".confirmTrainerWorkoutDeleteLink").live("click", function (e) {
      e.preventDefault();
      var targetUrl = $(this).attr("href");

      $("#all_dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons: {
          "Yes, Please remove all occurences of this workout": function () {
            $.post(
              "/member/remove_trainer_workout",
              { id: targetUrl },
              function (data) {
                if (data.success == "true") {
                  $("<h1>" + $("#log_date").html() + "</h1>").insertBefore(
                    "#logbook_container"
                  );
                  $("#logbook_container").html(
                    "<p>Your client does not have any workouts on this day</p>"
                  );
                }
                $("#all_dialog").dialog("close");
              },
              "json"
            );
          },
          Cancel: function () {
            $(this).dialog("close");
          },
        },
      });

      $("#all_dialog").dialog("open");
    });

    $(".confirmTrainerGroupWorkoutDeleteLink").live("click", function (e) {
      e.preventDefault();
      var targetUrl = $(this).attr("href");

      $("#group_all_dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons: {
          "Yes, Please remove all occurences of this workout": function () {
            $.post(
              "/member/remove_trainer_group_workout",
              { id: targetUrl },
              function (data) {
                if (data.success == "true") {
                  $("<h1>" + $("#log_date").html() + "</h1>").insertBefore(
                    "#logbook_container"
                  );
                  $("#logbook_container").html(
                    "<p>Your client does not have any workouts on this day</p>"
                  );
                }
                $("#group_all_dialog").dialog("close");
              },
              "json"
            );
          },
          Cancel: function () {
            $(this).dialog("close");
          },
        },
      });

      $("#group_all_dialog").dialog("open");
    });
  });
})(jQuery);
