(function ($) {
  function parse_date(string) {
    var date = new Date();
    var parts = String(string).split(/[- :]/);

    date.setFullYear(parts[0]);
    date.setMonth(parts[1] - 1);
    date.setDate(parts[2]);
    date.setHours(0);
    date.setMinutes(0);
    date.setSeconds(0);
    date.setMilliseconds(0);

    return date;
  }

  $(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

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

    // $('input.date').daterangepicker({
    // 	presetRanges:[
    // 		{text:'Today',dateStart:'Today',dateEnd:'Today'},
    // 		{text:'Next Month',dateStart:month + "/" + day + "/" + year,dateEnd:month1 + "/" + day1 + "/" + year1},
    // 		{text:'Next 3 Months',dateStart:month + "/" + day + "/" + year,dateEnd:month3 + "/" + day3 + "/" + year3}],
    // 	arrows:true,
    // 	earliestDate:Date.parse(month + "/" + day + "/" + year),
    // 	presets:{
    // 		specificDate:'Specific Date',
    // 		dateRange:'Date Range'
    // 	},
    // 	onChange:function(){
    // 		var selectedDates = $('input.date').val().toString();
    // 		if(selectedDates.length > 11){
    // 			$("#week_days").show();
    // 		}else{
    // 			$("#week_days").hide();
    // 		}
    // 	}
    // });

    $("#week_days").hide();

    $("select#client").change(function () {
      if ($(this).val() != "") {
        $("ul#workout_list").html("");
        $("input.equipment").attr("checked", "");
        $.post(
          "/member/generator_get_client",
          { id: $(this).val() },
          function (data) {
            if (data.type == "client") {
              if (data.photo != "") {
                $("#photo").html(
                  '<img src="/images/member_photos/' + data.photo + '" />'
                );
              } else {
              }
              if (data.available_equipment != null) {
                equipment = data.available_equipment.split(",");
                $.each(equipment, function (index, value) {
                  $("input#equipment" + value).attr("checked", "checked");
                });
              }
            } else if (data.type == "group") {
              $("#photo").html(
                '<h3>Clients in Group</h3><ul id="group_clients"></ul>'
              );
              $.each(data.clients, function (index, value) {
                $("#group_clients").append(
                  "<li>" + value.first_name + " " + value.last_name + "</li>"
                );
              });
              equipment = data.available_equipment.split(",");
              $.each(equipment, function (index, value) {
                $("input#equipment" + value).attr("checked", "checked");
              });
            }
          },
          "json"
        );
      }
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
                updateWorkoutListControls();
              }
            )
            .fadeIn();
        }
      }
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
      //alert($("#date").val());
      var querystring =
        "action=save_workout&" +
        $("#workout_title").fieldSerialize() +
        "&" +
        $("#workout_id").fieldSerialize() +
        "&" +
        $("#group_workout_id").fieldSerialize() +
        "&" +
        $("#trainer_group_workout_id").fieldSerialize() +
        "&" +
        $("#trainer_workout_id").fieldSerialize() +
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

      if ($("#date").val() == "") {
        error = true;
        error_message += "<li>You must select a valid date or date range</li>";
      } else if ($("#workout_list").children("li").length == 0) {
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
          if ($(this).find(".section_rest") != undefined) {
            querystring += "-" + $(this).find(".section_rest").val();
          }
          $(this)
            .find("ul.workout_categories li ul li")
            .each(function (index2) {
              if (!error) {
                if ($(this).find("input.exercise_id").val() == "") {
                  error = true;
                  error_message +=
                    "<li>You have workout sections without exercises selected</li>";
                }
                var sets = "";
                var reps = "";
                var rest = "";
                var weight = "";
                var time = "";
                $(this)
                  .find("input.sets")
                  .each(function (index3) {
                    sets += $(this).val() + "|";
                  });
                $(this)
                  .find("select.reps")
                  .each(function (index3) {
                    reps += $(this).val() + "|";
                  });
                $(this)
                  .find("select.rest")
                  .each(function (index3) {
                    rest += $(this).val() + "|";
                  });
                $(this)
                  .find("input.weight")
                  .each(function (index3) {
                    weight += $(this).val() + "|";
                  });
                $(this)
                  .find("select.time")
                  .each(function (index3) {
                    time += $(this).val() + "|";
                  });

                querystring +=
                  "&workout[" +
                  index +
                  "][][" +
                  index2 +
                  "]=" +
                  $(this).find("input.category_id").val() +
                  "-" +
                  $(this).find("input.exercise_id").val() +
                  "-" +
                  sets +
                  "-" +
                  reps +
                  "-" +
                  rest +
                  "-" +
                  weight +
                  "-" +
                  time +
                  "-" +
                  $(this).find("select.set_type").val() +
                  "-" +
                  $(this).find("select.weight_option").val();
              }
            });
        });
      }
      //alert(querystring);
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

    $("#exercise_library li.exercise").draggable({
      handle: "span.ui-widget",
      revert: false,
      helper: "clone",
    });

    $(".add_section").click(function () {
      $("#section_dialog").dialog("open");
    });

    $("#section_dialog").dialog({
      bgiframe: true,
      autoOpen: false,
      height: 300,
      modal: true,
      buttons: {
        "Add New Section": function () {
          $.post(
            "/member/get_section",
            { id: $("#section_dropdown").val() },
            function (data) {
              $("#workout_list").append(data);
              updateWorkoutListControls();
            },
            "html"
          );

          $(this).dialog("close");
        },
        Cancel: function () {
          $(this).dialog("close");
        },
      },
      close: function () {
        //allFields.val('').removeClass('ui-state-error');
      },
    });

    if ($("#workout_id") != undefined && $("#workout_id").val() != "") {
      $.post(
        "/member/get_workout_details_for_generator",
        { id: $("#workout_id").val() },
        function (data) {
          if (data.success == "true") {
            $("#workout_title").val(data.details.title);
            $("#client").val(data.details.user_id);
            $("#client").trigger("change");

            var workoutDate = parse_date(data.details.workout_date);
            var month = workoutDate.getMonth() + 1;
            var day = workoutDate.getDate();
            var year = workoutDate.getFullYear();
            $("#date").val(month + "/" + day + "/" + year);
            $("#date").trigger("change");
          }
        },
        "json"
      );

      $("ul#workout_list").hide(function () {
        $("ul#workout_list").html("");
      });

      $("ul#workout_list")
        .load(
          "/member/get_workout_for_generator",
          { id: $("#workout_id").val() },
          function () {
            updateWorkoutListControls();
          }
        )
        .fadeIn();
    }

    if (
      $("#group_workout_id") != undefined &&
      $("#group_workout_id").val() != ""
    ) {
      $.post(
        "/member/get_workout_details_for_generator",
        { group_id: $("#group_workout_id").val() },
        function (data) {
          if (data.success == "true") {
            $("#workout_title").val(data.details.title);
            $("#client").val("group-" + data.details.trainer_group_id);
            $("#client").trigger("change");

            var workoutDate = parse_date(data.details.workout_date);
            var month = workoutDate.getMonth() + 1;
            var day = workoutDate.getDate();
            var year = workoutDate.getFullYear();
            $("#date").val(month + "/" + day + "/" + year);
            $("#date").trigger("change");
          }
        },
        "json"
      );

      $("ul#workout_list").hide(function () {
        $("ul#workout_list").html("");
      });

      $("ul#workout_list")
        .load(
          "/member/get_workout_for_generator",
          { group_id: $("#group_workout_id").val() },
          function () {
            updateWorkoutListControls();
          }
        )
        .fadeIn();
    }

    if (
      $("#trainer_workout_id") != undefined &&
      $("#trainer_workout_id").val() != ""
    ) {
      $.post(
        "/member/get_workout_details_for_generator",
        { trainer_workout_id: $("#trainer_workout_id").val() },
        function (data) {
          if (data.success == "true") {
            $("#workout_title").val(data.details.title);
            $("#client").val(data.details.user_id);
            $("#client").trigger("change");

            var workoutDate = new Date();
            var start_month = workoutDate.getMonth() + 1;
            var start_day = workoutDate.getDate();
            var start_year = workoutDate.getFullYear();

            if (data.details.end_date != "") {
              var end_workoutDate = parse_date(data.details.end_date);
              var end_month = end_workoutDate.getMonth() + 1;
              var end_day = end_workoutDate.getDate();
              var end_year = end_workoutDate.getFullYear();
              $("#date").val(
                start_month +
                  "/" +
                  start_day +
                  "/" +
                  start_year +
                  " - " +
                  end_month +
                  "/" +
                  end_day +
                  "/" +
                  end_year
              );
            } else {
              $("#date").val(start_month + "/" + start_day + "/" + start_year);
            }

            $("#date").trigger("change");

            var days = data.details.days.split(",");
            for (var i = 0; i < days.length; i++) {
              $("input.days[value=" + days[i] + "]").attr("checked", true);
            }

            $("ul#workout_list").hide(function () {
              $("ul#workout_list").html("");
            });

            $("ul#workout_list")
              .load(
                "/member/get_workout_for_generator",
                { id: data.details.workout_id },
                function () {
                  updateWorkoutListControls();
                }
              )
              .fadeIn();
          }
        },
        "json"
      );
    }

    if (
      $("#trainer_group_workout_id") != undefined &&
      $("#trainer_group_workout_id").val() != ""
    ) {
      $.post(
        "/member/get_workout_details_for_generator",
        { trainer_group_workout_id: $("#trainer_group_workout_id").val() },
        function (data) {
          if (data.success == "true") {
            $("#workout_title").val(data.details.title);
            $("#client").val("group-" + data.details.trainer_group_id);
            $("#client").trigger("change");

            var workoutDate = new Date();
            var start_month = workoutDate.getMonth() + 1;
            var start_day = workoutDate.getDate();
            var start_year = workoutDate.getFullYear();

            if (data.details.end_date != "") {
              var end_workoutDate = parse_date(data.details.end_date);
              var end_month = end_workoutDate.getMonth() + 1;
              var end_day = end_workoutDate.getDate();
              var end_year = end_workoutDate.getFullYear();
              $("#date").val(
                start_month +
                  "/" +
                  start_day +
                  "/" +
                  start_year +
                  " - " +
                  end_month +
                  "/" +
                  end_day +
                  "/" +
                  end_year
              );
            } else {
              $("#date").val(start_month + "/" + start_day + "/" + start_year);
            }

            $("#date").trigger("change");

            var days = data.details.days.split(",");
            for (var i = 0; i < days.length; i++) {
              $("input.days[value=" + days[i] + "]").attr("checked", true);
            }

            $("ul#workout_list").hide(function () {
              $("ul#workout_list").html("");
            });

            $("ul#workout_list")
              .load(
                "/member/get_workout_for_generator",
                { id: data.details.workout_id },
                function () {
                  updateWorkoutListControls();
                }
              )
              .fadeIn();
          }
        },
        "json"
      );
    }

    $("#client").trigger("change");
  });

  function updateWorkoutListControls() {
    $(".add_exercise")
      .unbind("click")
      .click(function () {
        clicked_item = $(this);
        $("#exercise_dialog").dialog("open");
        return false;
      });

    $('a[class^="select_exercise"]')
      .unbind("click")
      .click(function () {
        clicked_item = $(this);
        $("input#current_exercise_edit").val($(this).attr("id").substring(9));
        $("#dialog_" + $(this).attr("class").substring(15)).dialog("open");
        return false;
      });

    $("li.category").droppable("destroy");
    $("li.category").droppable({
      accept: ".exercise",
      activeClass: "ui-state-hover",
      hoverClass: "ui-state-active",
      tolerance: "pointer",
      greedy: true,
      drop: function (event, ui) {
        $(this).find(".exercise_id").val(ui.draggable.find("a").attr("id"));
        $(this)
          .find("a.play-exercise")
          .html(ui.draggable.find("span.ex_title").html());
        $(this)
          .find("a.play-exercise")
          .attr("href", ui.draggable.find("a").attr("href"));
      },
    });

    $("li.section").droppable("destroy");
    $("li.section").droppable({
      accept: ".exercise",
      activeClass: "ui-state-hover",
      hoverClass: "ui-state-active",
      tolerance: "pointer",
      drop: function (event, ui) {
        clicked_item = $(this);
        $.post(
          "/member/get_exercise_type",
          { exercise_id: ui.draggable.find("a").attr("id") },
          function (data) {
            clicked_item.children("ul").append(data);
            updateWorkoutListControls();
          },
          "html"
        );
        /*$( this )
				.find( "a.play-exercise" )
					.html( ui.draggable.find('span.ex_title').html() );
			$( this )
				.find( "a.play-exercise" )
					.attr('href', ui.draggable.find('a').attr('href') );*/
      },
    });

    $(".remove")
      .unbind("click")
      .click(function () {
        $(this)
          .parent()
          .fadeOut(function () {
            $(this).remove();
          });
      });

    $(".remove_exercise")
      .unbind("click")
      .click(function () {
        $(this)
          .parents("li.category")
          .first()
          .fadeOut(function () {
            $(this).remove();
          });
        return false;
      });

    $(".add_set")
      .unbind("click")
      .click(function () {
        $(this)
          .parents("table")
          .first()
          .children("tbody")
          .first()
          .append(
            $(this)
              .parents("table")
              .first()
              .children("tbody")
              .first()
              .children("tr.bottom")
              .first()
              .removeClass("bottom")
              .clone()
              .addClass("bottom")
          );
        $(this)
          .parents("table")
          .first()
          .children("tbody")
          .first()
          .find("tr.bottom")
          .find("span.set_number")
          .html(
            parseInt(
              $(this)
                .parents("table")
                .first()
                .children("tbody")
                .first()
                .find("tr.bottom")
                .find("span.set_number")
                .html()
            ) + 1
          );
        $(this)
          .parents("table")
          .first()
          .children("tbody")
          .first()
          .find("tr.bottom")
          .find("td.ex_options")
          .remove();
        $(this)
          .parents("table")
          .first()
          .children("tbody")
          .first()
          .children("tr")
          .first()
          .children("td")
          .first()
          .attr(
            "rowspan",
            parseInt(
              $(this)
                .parents("table")
                .first()
                .children("tbody")
                .first()
                .children("tr")
                .first()
                .children("td")
                .first()
                .attr("rowspan")
            ) + 1
          );
        return false;
      });

    $(".remove_set")
      .unbind("click")
      .click(function () {
        if (
          $(this)
            .parents("table")
            .first()
            .children("tbody")
            .first()
            .children("tr").length > 1
        ) {
          $(this)
            .parents("table")
            .first()
            .children("tbody")
            .first()
            .children("tr.bottom")
            .first()
            .remove();
          $(this)
            .parents("table")
            .first()
            .children("tbody")
            .first()
            .children("tr")
            .last()
            .addClass("bottom");
        }
        return false;
      });

    $(".remove_section")
      .unbind("click")
      .click(function () {
        $(this).parents("li").first().remove();
        return false;
      });

    //$('.workout_exercises').toggle();
    //$('.workout_categories').toggle();
    $(".section_title")
      .unbind("click")
      .click(function () {
        $(this).removeClass("off").addClass("on");
        $(this).parent().find(".workout_categories").toggle();
        return false;
      });

    $(".workout_category_title")
      .unbind("click")
      .click(function () {
        $(this).removeClass("off").addClass("on");
        $(this).parent().find(".workout_exercises").toggle();
        return false;
      });

    $(".edit_sets")
      .unbind("click")
      .click(function () {
        $("input#current_exercise_edit").val($(this).attr("id").substring(5));
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

    $("#workout_list select.set_type").each(function () {
      if ($(this).val() == "sets_reps") {
        $(this).parents("table").first().find("select.reps").show();
        $(this).parents("table").first().find("select.time").hide();
      } else if ($(this).val() == "sets_time") {
        $(this).parents("table").first().find("select.reps").hide();
        $(this).parents("table").first().find("select.time").show();
      }
    });

    $("#workout_list select.weight_option").each(function () {
      if ($(this).val() == "weighted") {
        $(this).parents("table").first().find("span.weight_input_box").show();
        $(this).parents("table").first().find("span.bodyweight").hide();
      } else if ($(this).val() == "bodyweight") {
        $(this).parents("table").first().find("span.weight_input_box").hide();
        $(this).parents("table").first().find("span.bodyweight").show();
      }
    });

    $("#workout_list select.set_type").change(function () {
      if ($(this).val() == "sets_reps") {
        $(this).parents("table").first().find("select.reps").show();
        $(this).parents("table").first().find("select.time").hide();
      } else if ($(this).val() == "sets_time") {
        $(this).parents("table").first().find("select.reps").hide();
        $(this).parents("table").first().find("select.time").show();
      }
    });

    $("#workout_list select.weight_option").change(function () {
      if ($(this).val() == "weighted") {
        $(this).parents("table").first().find("span.weight_input_box").show();
        $(this).parents("table").first().find("span.bodyweight").hide();
      } else if ($(this).val() == "bodyweight") {
        $(this).parents("table").first().find("span.weight_input_box").hide();
        $(this).parents("table").first().find("span.bodyweight").show();
      }
    });
  }
})(jQuery);
