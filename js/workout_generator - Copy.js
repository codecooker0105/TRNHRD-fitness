(function ($) {
  $(document).ready(function () {
    $(".play-exercise").colorbox();
    $(".exercises").toggle();
    $(".levels").toggle();
    $(".muscle_title").click(function () {
      $(this).removeClass("off").addClass("on");
      $(this).parent().find(".levels").toggle();
      return false;
    });

    $(".level_title").click(function () {
      $(this).removeClass("off").addClass("on");
      $(this).parent().find(".exercises").toggle();
      return false;
    });
    var currentTime = new Date();
    var month = currentTime.getMonth() + 1;
    var day = currentTime.getDate();
    var year = currentTime.getFullYear();

    var oneMonth = new Date();
    oneMonth.setMonth(oneMonth.getMonth() + 1);
    var month1 = oneMonth.getMonth() + 1;
    var day1 = oneMonth.getDate();
    var year1 = oneMonth.getFullYear();

    var threeMonth = new Date();
    threeMonth.setMonth(threeMonth.getMonth() + 3);
    var month3 = threeMonth.getMonth() + 1;
    var day3 = threeMonth.getDate();
    var year3 = threeMonth.getFullYear();

    $("input.date").daterangepicker({
      presetRanges: [
        { text: "Today", dateStart: "Today", dateEnd: "Today" },
        {
          text: "Next Month",
          dateStart: month + "/" + day + "/" + year,
          dateEnd: month1 + "/" + day1 + "/" + year1,
        },
        {
          text: "Next 3 Months",
          dateStart: month + "/" + day + "/" + year,
          dateEnd: month3 + "/" + day3 + "/" + year3,
        },
      ],
      arrows: true,
      earliestDate: Date.parse(month + "/" + day + "/" + year),
      presets: {
        specificDate: "Specific Date",
        dateRange: "Date Range",
      },
      onChange: function () {
        var selectedDates = $("input.date").val().toString();
        if (selectedDates.length > 11) {
          $("#week_days").show();
        } else {
          $("#week_days").hide();
        }
      },
    });

    $("#week_days").hide();

    $("select#client").change(function () {
      $("ul#workout_list").html("");
      $("input.equipment").attr("checked", "");
      $.post(
        "/member/get_client",
        { id: $(this).val() },
        function (data) {
          if (data.photo != "") {
            $("#photo").html(
              '<img src="/images/member_photos/' + data.photo + '" />'
            );
          } else {
          }
          equipment = data.available_equipment.split(",");
          $.each(equipment, function (index, value) {
            $("input#equipment" + value).attr("checked", "checked");
          });
        },
        "json"
      );
    });

    $("input#generate").click(function () {
      if ($("select.progression").val() == "") {
        alert("You must select a progression first");
      } else if ($("select.skeleton").val() == "") {
        alert("You must select a workout first");
      } else {
        $("ul#workout_list").hide(function () {
          $("ul#workout_list").html("");
        });

        var equipment = $(".equipment:checked")
          .map(function (i, n) {
            return $(n).val();
          })
          .get(); //get converts it to an array

        if (equipment.length == 0) {
          equipment = "none";
        }
        if ($(this).val() != "") {
          $("ul#workout_list")
            .load(
              "/member/skeleton_json",
              {
                id: $("select.skeleton").val(),
                progression_id: $("select.progression").val(),
                user_id: $("#client").val(),
                available_equipment: equipment,
              },
              function () {
                //$("ul#workout_list").fadeIn();
                $('a[class^="select_exercise"]').click(function () {
                  $("input#current_exercise_edit").val(
                    $(this).attr("id").substring(9)
                  );
                  $("#dialog_" + $(this).attr("class").substring(15)).dialog(
                    "open"
                  );
                  return false;
                });

                $(".remove").click(function () {
                  $(this)
                    .parent()
                    .fadeOut(function () {
                      $(this).remove();
                    });
                });

                $(".workout_exercises").toggle();
                $(".workout_categories").toggle();
                $(".section_title").click(function () {
                  $(this).removeClass("off").addClass("on");
                  $(this).parent().find(".workout_categories").toggle();
                  return false;
                });

                $(".workout_category_title").click(function () {
                  $(this).removeClass("off").addClass("on");
                  $(this).parent().find(".workout_exercises").toggle();
                  return false;
                });

                $(".expand_collapse").click(function () {
                  $(this)
                    .parent()
                    .children("ul")
                    .toggle("normal", function () {
                      if (
                        $(this)
                          .parent()
                          .children(".expand_collapse")
                          .hasClass("ui-icon-circle-triangle-n")
                      ) {
                        $(this)
                          .parent()
                          .children(".expand_collapse")
                          .removeClass("ui-icon-circle-triangle-n");
                        $(this)
                          .parent()
                          .children(".expand_collapse")
                          .addClass("ui-icon-circle-triangle-s");
                      } else {
                        $(this)
                          .parent()
                          .children(".expand_collapse")
                          .removeClass("ui-icon-circle-triangle-s");
                        $(this)
                          .parent()
                          .children(".expand_collapse")
                          .addClass("ui-icon-circle-triangle-n");
                      }
                    });
                });

                $(".add_exercise").click(function () {
                  section_item = $(this).attr("id");
                  return section_item;
                });

                $(".add_exercise").click(function () {
                  $("#exercise_dialog").dialog("open");
                });

                $(".edit_sets").click(function () {
                  $("input#current_exercise_edit").val(
                    $(this).attr("id").substring(5)
                  );
                  $("#sets_dropdown").val(
                    $(this).parent().parent().find("input.sets").val()
                  );
                  $("#reps_dropdown").val(
                    $(this).parent().parent().find("input.reps").val()
                  );
                  $("#sets_reps_dialog").dialog("open");
                  return false;
                });

                $(".play-exercise").colorbox();

                $("#workout_list").sortable({ axis: "y", handle: ".move" });
                $("#workout_list ul.workout_categories").sortable({
                  axis: "y",
                  handle: ".move",
                });
                $(".expand_collapse").trigger("click");
                //$("ul#workout_list").fadeIn();
              }
            )
            .fadeIn();
        }
      }
    });

    $("#sets_reps_dialog").dialog({
      bgiframe: true,
      autoOpen: false,
      height: 300,
      modal: true,
      buttons: {
        "Update Sets & Reps": function () {
          select_item = $("input#current_exercise_edit").val();
          $("#sets_" + select_item)
            .parent()
            .find("input.sets")
            .val($("#sets_dropdown").val());
          $("#sets_" + select_item)
            .parent()
            .find("input.sets")
            .val($("#reps_dropdown").val());
          $("#sets_" + select_item)
            .parent()
            .find(".sets_reps_time")
            .html(
              $("#sets_dropdown").val() +
                " Sets x " +
                $("#reps_dropdown").val() +
                " Reps"
            );
          $(".play-exercise").colorbox();
          $(this).dialog("close");
        },
        Close: function () {
          $(this).dialog("close");
        },
      },
      close: function () {
        //allFields.val('').removeClass('ui-state-error');
      },
    });

    $("#complete_dialog").dialog({
      bgiframe: true,
      autoOpen: false,
      height: 300,
      modal: true,
      buttons: {
        "Create New Workout": function () {
          location.href = "/fit-calculator";
        },
        Close: function () {
          $(this).dialog("close");
        },
      },
      close: function () {
        //allFields.val('').removeClass('ui-state-error');
      },
    });

    $("#error_dialog").dialog({
      bgiframe: true,
      autoOpen: false,
      height: 300,
      modal: true,
      buttons: {
        "Return to Calculator": function () {
          $(this).dialog("close");
        },
      },
      close: function () {
        //allFields.val('').removeClass('ui-state-error');
      },
    });

    $("#equipment_dialog").dialog({
      bgiframe: true,
      autoOpen: false,
      height: 300,
      modal: true,
      buttons: {
        "Reset Workout": function () {
          $("ul#workout_list").html("");
          $(".skeleton_selector")
            .find("option:first")
            .attr("selected", "selected")
            .parent("select");
          $(this).dialog("close");
        },
        "Leave Available Equipment As Is": function () {
          $(this).dialog("close");
        },
      },
      close: function () {
        //allFields.val('').removeClass('ui-state-error');
      },
    });

    $("#workout_generator_form").submit(function () {
      alert($("#date").val());
      var querystring =
        "action=save_workout&" +
        $("#workout_title").fieldSerialize() +
        "&" +
        $("#client").fieldSerialize() +
        "&" +
        $(".progression").fieldSerialize() +
        "&" +
        $(".skeleton").fieldSerialize() +
        "&date=" +
        $("#date").val();
      var fields = $("input.days").serializeArray();
      jQuery.each(fields, function (i, field) {
        querystring += "&days[]=" + field.value;
      });
      var error = false;
      var error_message = "The following errors have occurred:<ul>";

      if ($("#workout_list").children("li").length == 0) {
        error = true;
        error_message +=
          "<li>You must have at least 1 section to your workout or select a skeleton workout</li>";
      } else {
        $("#workout_list li.section").each(function (index) {
          querystring +=
            "&workout[" +
            index +
            "][]=" +
            $(this).children("input.section_id").fieldValue();
          $(this)
            .find("ul.workout_categories li ul li")
            .each(function (index2) {
              if (!error) {
                if ($(this).find("input.exercise_id").val() == "") {
                  error = true;
                  error_message +=
                    "<li>You have workout sections without exercises selected</li>";
                }
                querystring +=
                  "&workout[" +
                  index +
                  "][][" +
                  index2 +
                  "]=" +
                  $(this).find("input.category_id").val() +
                  "-" +
                  $(this).children("input.exercise_id").val() +
                  "-" +
                  $(this).children("input.sets").val() +
                  "-" +
                  $(this).children("input.reps").val() +
                  "-" +
                  $(this).children("input.time").val();
              }
            });
        });
      }
      alert(querystring);
      if (error) {
        error_message += "</ul>";
        $("#error_dialog").html(error_message);
        $("#error_dialog").dialog("open");
      } else {
        $.post("/member/process_generator", querystring, function (data) {
          $("#complete_dialog").html(data);
          $("#complete_dialog").dialog("open");
        });
      }
      return false;
    });

    function showResponse(responseText, statusText) {
      $("#complete_dialog").dialog("open");
    }

    var $sidebar = $("#library_wrapper"),
      $window = $(window),
      offset = $sidebar.offset(),
      topPadding = 15;

    $window.scroll(function () {
      if ($window.scrollTop() > offset.top) {
        $sidebar.stop().animate({
          marginTop: $window.scrollTop() - offset.top + topPadding,
        });
      } else {
        $sidebar.stop().animate({
          marginTop: 0,
        });
      }
    });
  });
})(jQuery);
