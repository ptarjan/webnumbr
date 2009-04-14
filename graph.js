var api = "ajax/graph.php?id=" + $.query.get("id");
$.getJSON(api, function (json) {
    $("#data").ready(function () {
        var dataNode = $("#data")[0];
        if (dataNode) {
            $("#data").hide();

            json.graph['json API'] = api + "&callback=cbfunc";
            json.graph.embed = '<input value="&lt;iframe src=&quot;http://paul.slowgeek.com/webGrapher/embedGraph.php?id=' + $.query.get("id") + '&amp;type=js&quot; frameborder=&quot;0&quot; style=&quot;width: 450px; height: 300px; display: block;&quot; ></iframe>" size="100" onclick="javascript:this.focus(); javascreipt:this.select()" />';
            for (var key in json.graph) {
                var tr = document.createElement("tr");
                var td = document.createElement("th");
                td.innerHTML = '<b>' + key + '</b>';
                tr.appendChild(td);
                td = document.createElement("td");
                switch (key) {
                    case "url" : 
                    case "json API" :
                        td.innerHTML = '<a href="' + (json.graph[key]) + '">' + json.graph[key] + '</a>'; break;
                    case "xpath" : td.innerHTML = '<a href="selectNode.php?' + $.param({url: json.graph.url, xpath: json.graph.xpath}) + '">' + json.graph[key] + '</a>'; break;
                    default : td.innerHTML = json.graph[key];
                }
                tr.appendChild(td);
                dataNode.appendChild(tr);
            }
        }
    });

    $("#title").ready(function () {
        $("#title").text(json.graph.name);
    });
    document.title = $("head title").append(" - " + json.graph.name).text();

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
        $("#data").show("normal");
        return false;
    });

    if (typeof callback == "function") { callback(json); }
});

