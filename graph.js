if (! $.query.get("id")) {
    $("#title").ready(function() {
        $("#title").text("id is a required parameter");
    });
} else {

$.getJSON("ajax/v1/graph" + $.query, function (json) {
    $(document).ready(function () {
        if (typeof paulisageek != "undefined" && typeof paulisageek.wg != "undefined" && typeof paulisageek.wg.finishedAjaxCallback == "function") { 
            paulisageek.wg.finishedAjaxCallback(json);
        }
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

            if (series.length === 0 && json.graphs.length == 1) {
                function getLetterGraph(s) {
                    var letters = {
                        "N" : [[0, 0], [0, 1], [1, 0], [1, 1], [1, 0]],
                        "O" : [[2, 0], [1, 0.2], [0, 0.5], [1, 0.7], [2, 0], [3, 0.7], [4, 0.5], [3, 0.2], [2, 0]],
                        " " : [[9, 0]],
                        "D" : [[0, 0], [0, 1], [2, 0.8], [2, 0.2], [0, 0]],
                        "A" : [[0, 0], [1, 1], [1.5, 0.5], [0.5, 0.5], [1.5, 0.5], [2, 0]],
                        "T" : [[2, 0], [2, 1], [0, 1], [4, 1], [2, 1], [2, 0]],
                    }
                    return $.map(s, function(a) { return letters[a]});
                }
                   
                if (graph.meta.goodFetches == 0 && graph.meta.badFetches == 0) {
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
                } else {
                    series = [
                        [0, 0],
                        [2, 0], [2, 1], [4, 0], [4, 1], [4, 0], // N
                        [7, 0], [6, 0.15], [5, 0.5], [6, 0.85], [7, 1], [8, 0.85], [9, 0.5], [8, 0.15], [7, 0], // O
                        [16, 0], [16, 1], [18, 0.8], [18, 0.2], [16, 0], // D
                        [21, 0], [22, 1], [22.5, 0.5], [21.5, 0.5], [22.5, 0.5], [23, 0], // A
                        [27, 0], [27, 1], [25, 1], [29, 1], [27, 1], [27, 0], // T
                        [31, 0], [32, 1], [32.5, 0.5], [31.5, 0.5], [32.5, 0.5], [33, 0], // A
                        [35, 0]
                    ];
                }
            }
            var a = $("<a/>").attr("href", "http://paulisageek.com/webGraphr/graph?id=" + graph.meta.id).text(graph.meta.name);
            a.attr("title", a.text());
            if (a.text().length > 30) 
                a.text(a.text().substring(0, 27) + "...");
            data.push({
                "label" : $("<div/>").append(a).html(), // htmlspecialchars
                "data" : series
            });
        }
        document.title = $("head title").text($("head title").text() + " - " + $("#title").text()).text();
        $("#title").attr("title", $("#title").text());

        if (typeof paulisageek != "undefined" && typeof paulisageek.wg != "undefined" && typeof paulisageek.wg.preGraphCallback == "function") { 
            paulisageek.wg.preGraphCallback(json);
        }
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

        if ($("#title").text().length > 80) {
            $("#title").text($("#title").text().substring(0,77) + "...");
        }

        if (typeof paulisageek != "undefined" && typeof paulisageek.wg != "undefined" && typeof paulisageek.wg.postGraphCallback == "function") { 
            paulisageek.wg.postGraphCallback(json);
        }
    });
});

}
