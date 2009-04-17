if (! $.query.get("id")) {
    $("#title").ready(function() {
        $("#title").text("id is a required parameter");
    });
} else {

$.getJSON("ajax/v1/graph.php" + $.query, function (json) {
    $(document).ready(function () {
        var data = [];

        for (var graphIndex in json.graphs) {
           var graph = json.graphs[graphIndex];

            $("#title").ready(function () {
                if ($("#title").text().length > 0) { $("#title").append(" + "); }
                $("#title").text($("#title").text() + graph.meta.name);
            });

            var series = [];
            var offset = new Date().getTimezoneOffset();
            for (var i=0; i < graph.data.length; i++) {
                var time = graph.data[i][0] - offset * 60;
                series.push([time * 1000, graph.data[i][1]]);
            }

            if (series.length === 0) {
                series = [
                    [0, 1], [1, 0], [2, 1], [3, 0], [4, 1], [3, 0], // W
                    [6, 0], [7, 1], [7.5, 0.5], [6.5, 0.5], [7.5, 0.5], [8, 0], // A
                    [10, 0], [10, 1], [10, 0], // I
                    [13, 0], [13, 1], [11, 1], [15, 1], [13, 1], [13, 0],  // T
                    [22, 0], [22, 0.5], [19, 0.5], [22, 1], [22, 0],  // 4
                    [31, 0], [31, 1], [32, 0.9], [33, 0.8], [33, 0.2], [32, 0.1], [31, 0], // D
                    [34, 0], [35, 1], [35.5, 0.5], [34.5, 0.5], [35.5, 0.5], [36, 0], // A
                    [38, 0], [38, 1], [36, 1], [40, 1], [38, 1], [38, 0], // T
                    [40, 0], [41, 1], [41.5, 0.5], [40.5, 0.5], [41.5, 0.5], [42, 0] // A
                ];
            }
            data.push({
                "label" : $("<div/>").text(graph.meta.name).html(), // htmlspecialchars
                "data" : series
            });
        }
        document.title = $("head title").text($("head title").text() + " - " + $("#title").text()).text();

        var config = {
            xaxis: { mode : "time" },
            legend : { 
                position : "nw",
                show : data.length > 1
            },
            lines : { show : true }
        };
        $("#plot").ready(function() {
            $.plot($('#plot'), data, config);
        });

        if (typeof paulisageek != "undefined" && typeof paulisageek.wg != "undefined" && typeof paulisageek.wg.graphCallback == "function") { 
            paulisageek.wg.graphCallback(json);
        }
    });
});

}
