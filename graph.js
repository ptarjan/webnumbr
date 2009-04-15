var api = "ajax/graph.php?id=" + $.query.get("id");
$.getJSON(api, function (json) {
    $("#data").ready(function () {
        if ($("#data").size() > 0) {
            $("#data").hide();

            json.graph['json API'] = api;
            json.graph.embed = "";
            for (var key in json.graph) {
                var tr = $(document.createElement("tr"));
                var th = $(document.createElement("th"));
                th.text(key).wrapInner("<b></b>");
                tr.append(th);
                var td = $(document.createElement("td"));
                td.text(json.graph[key]);
                switch (key) {
                    case "url" : 
                    case "json API" :
                        // Create an "a" around the element with the same content as the element
                        td.wrapInner($(document.createElement("a")).attr("href", td.text()))
                        break;
                    case "embed" :
                        var iframe = $(document.createElement("iframe"))
                            .attr("frameborder", "0")
                            .attr("src", "http://paulisageek.com/webGrapher/embedGraph.php?id=" + $.query.get("id") + "&type=js")
                            .css("width", "450px")
                            .css("height", "300px")
                            .css("display", "block")
                        ;
                        var input = $(document.createElement("input"))
                            .attr("onclick", "javascript:this.focus(); javascript:this.select()")
                            .attr("value", $(document.createElement("div")).append(iframe).html())
                            .attr("size", 90)
                        ;
                        td.append(input);
                        var a = $(document.createElement("a"))
                            .attr("href",  "#")
                            .attr("onclick", 'javascript:window.open(\'embedGraph.php?id=' + $.query.get("id") + '&type=js\', \'Embed Preview\', \'width=450,height=300\'); return false;')
                            .text("Preview")
                        ;
                        td.append(" ");
                        td.append(a);
                        break;
                    case "xpath" :
                        td.wrapInner($(document.createElement("a")).attr("href", 'selectNode.php?' + $.param({url: json.graph.url, xpath: json.graph.xpath})))
                        break;
                    case "createdTime" :
                    case "modifiedTime" :
                        td.text(new Date(json.graph[key] * 1000).toString());
                        break;
                }
                tr.append(td);
                $("#data").append(tr);
            }
        }
    });

    $("#title").ready(function () {
        $("#title").text(json.graph.name);
    });
    document.title = $("head title").text($("head title").text() + " - " + $("#title").text()).text();

    var data = [];
    var offset = new Date().getTimezoneOffset();
    for (var i=0; i < json.data.length; i++) {
        var time = json.data[i][0] - offset * 60;
        data.push([time * 1000, json.data[i][1]]);
    }

    if (data.length === 0) {
        data = [
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

    var config = {
        xaxis: { mode : "time" }
    };

    $("#plot").ready(function() {
        $.plot($('#plot'), [data], config);
    });

    $("#graphinfo").click(function() {
        $("#data").toggle("normal");
        return false;
    });

    if (typeof callback == "function") { callback(json); }
});

