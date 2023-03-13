var chart;
(function ($) {
  $(document).ready(function () {
    var options = {
      chart: {
        renderTo: "dashboard",
        zoomType: "x",
        spacingRight: 20,
        defaultSeriesType: "areaspline",
      },
      title: {
        text: "Personal Statistics",
      },
      subtitle: {
        text:
          document.ontouchstart === undefined
            ? "Click and drag in the plot area to zoom in"
            : "Drag your finger over the plot to zoom in",
      },
      xAxis: {
        type: "datetime",
        maxZoom: 14 * 24 * 3600000, // fourteen days
        title: {
          text: null,
        },
      },
      yAxis: {
        title: {
          text: "Value",
        },
        startOnTick: false,
        showFirstLabel: false,
      },
      tooltip: {
        shared: true,
      },
      legend: {
        enabled: true,
      },
      plotOptions: {
        area: {
          stacking: "normal",
          lineColor: "#666666",
          lineWidth: 1,
          marker: {
            lineWidth: 1,
            lineColor: "#666666",
          },
          shadow: false,
          states: {
            hover: {
              lineWidth: 1,
            },
          },
        },
      },

      series: [],
    };

    $.post(
      "/member/get_stats_chart",
      "interval=weekly",
      function (stats_data) {
        stat_count = 0;
        $.each(stats_data, function (i, stat_type) {
          stat_totals = [];
          options.series[stat_count] = new Object();
          $.each(stat_type, function (j, stat_value) {
            if (options.series[stat_count].pointStart == undefined) {
              options.series[stat_count].pointStart = Date.parse(
                stat_value.week_start + " UTC"
              );
            }
            options.series[stat_count].name = stat_value.title;
            stat_totals.push(parseInt(stat_value.average.replace(",", ""), 10));
          });
          options.series[stat_count].pointInterval = 24 * 3600 * 1000 * 7;
          options.series[stat_count].data = stat_totals;
          options.series[stat_count].type = "line";

          stat_count++;
        });

        chart = new Highcharts.Chart(options);
      },
      "json"
    );

    $("#chart_form").submit(function () {
      $.post(
        "/member/get_stats_chart",
        "interval=" + $("#interval").val(),
        function (stats_data) {
          if ($("#interval").val() == "daily") {
            stat_count = 0;
            $.each(stats_data, function (i, stat_type) {
              stat_totals = [];
              options.series[stat_count] = new Object();
              $.each(stat_type, function (j, stat_value) {
                if (options.series[stat_count].pointStart == undefined) {
                  options.series[stat_count].pointStart = Date.parse(
                    stat_value.day + " UTC"
                  );
                }
                options.series[stat_count].name = stat_value.title;
                stat_totals.push(
                  parseInt(stat_value.average.replace(",", ""), 10)
                );
              });
              options.series[stat_count].pointInterval = 24 * 3600 * 1000;
              options.series[stat_count].data = stat_totals;
              options.series[stat_count].type = "line";

              stat_count++;
            });
            chart = new Highcharts.Chart(options);
          } else if ($("#interval").val() == "weekly") {
            stat_count = 0;
            $.each(stats_data, function (i, stat_type) {
              stat_totals = [];
              options.series[stat_count] = new Object();
              $.each(stat_type, function (j, stat_value) {
                if (options.series[stat_count].pointStart == undefined) {
                  options.series[stat_count].pointStart = Date.parse(
                    stat_value.week_start + " UTC"
                  );
                }
                options.series[stat_count].name = stat_value.title;
                stat_totals.push(
                  parseInt(stat_value.average.replace(",", ""), 10)
                );
              });
              options.series[stat_count].pointInterval = 24 * 3600 * 1000 * 7;
              options.series[stat_count].data = stat_totals;
              options.series[stat_count].type = "line";

              stat_count++;
            });

            chart = new Highcharts.Chart(options);
          } else if ($("#interval").val() == "monthly") {
            stat_count = 0;
            $.each(stats_data, function (i, stat_type) {
              stat_totals = [];
              options.series[stat_count] = new Object();
              $.each(stat_type, function (j, stat_value) {
                if (options.series[stat_count].pointStart == undefined) {
                  options.series[stat_count].pointStart = Date.parse(
                    stat_value.month_start + " UTC"
                  );
                }
                options.series[stat_count].name = stat_value.title;
                stat_totals.push(
                  parseInt(stat_value.average.replace(",", ""), 10)
                );
              });
              options.series[stat_count].pointInterval = 24 * 3600 * 1000 * 30;
              options.series[stat_count].data = stat_totals;
              options.series[stat_count].type = "line";

              stat_count++;
            });

            chart = new Highcharts.Chart(options);
          }
        },
        "json"
      );
      return false;
    });
  });
})(jQuery);
