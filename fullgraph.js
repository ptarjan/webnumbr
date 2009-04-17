if (typeof paulisageek == "undefined") { paulisageek = {}; }
if (typeof paulisageek.wg == "undefined") { paulisageek.wg = {}; }

paulisageek.wg.postGraphCallback = function(json) {
    $("#graphinfo").click(function() {
        $("#data").toggle("normal");
        return false;
    });

    var config = {
        showOn: 'button', 
        buttonImage: 'images/calendar.gif', 
        buttonImageOnly: true,
        onClose : function() { $("form#dateRange").submit() }
    };
    $(":input[name='to']").datepicker(config);
    $(":input[name='from']").datepicker(config);
    $(":input[name='from']").change(function() {
        $(":input[name='days']").val("");
    });
    $(":input[name='days']").change(function() {
        $(":input[name='from']").val("");
    });

    for (var graphIndex in json.graphs) {
       var graph = json.graphs[graphIndex];

        // If the node exists
        if ($("#data").size() > 0) {
            $("#data").hide();
            var meta = graph.meta;

            var query = $.query.keys;
            query.id = meta.id;
            query = $.param(query);

            var tbody = $(document.createElement("tbody"));
            meta.API = "ajax/v1/graph?" + query;
            meta.embed = "";
            for (var key in meta) {
                var tr = $(document.createElement("tr"));
                var th = $(document.createElement("th"));
                th.text(key).wrapInner("<b></b>");
                tr.append(th);
                var td = $(document.createElement("td"));
                td.text(meta[key]);
                switch (key) {
                    case "url" : 
                    case "API" :
                        // Create an "a" around the element with the same content as the element
                        td.wrapInner($(document.createElement("a")).attr("href", td.text()));
                        break;
                    case "embed" :
                        var iframe = $(document.createElement("iframe"))
                            .attr("frameborder", "0")
                            .attr("src", "http://paulisageek.com/webGraphr/embedGraph?type=js&" + query)
                            .css("width", "450px")
                            .css("height", "300px")
                            .css("display", "block");
                        var input = $(document.createElement("input"))
                            .click(function() {this.focus(); this.select(); })
                            .attr("value", $(document.createElement("div")).append(iframe).html())
                            .attr("size", 90);
                        td.append(input);
                        var a = $(document.createElement("a"))
                            .attr("href",  "#")
                            .click(function() {
                                window.open('embedGraph?type=js&' + query, 'Embed Preview', 'width=450,height=300'); 
                                return false;
                            })
                            .text("Preview");
                        td.append(" ");
                        td.append(a);
                        break;
                    case "xpath" :
                        td.wrapInner($(document.createElement("a")).attr("href", 'selectNode?' + $.param({url: meta.url, xpath: meta.xpath})));
                        break;
                    case "createdTime" :
                    case "modifiedTime" :
                        td.text(new Date(meta[key] * 1000).toString());
                        break;
                }
                tr.append(td);
                tbody.append(tr);
            }
            // 0 for the first one, 30 for others
            var margin = ($("#data").children().length > 0 ? 30 : 0);
            $("#data").append($(document.createElement("table")).append(tbody).css("margin-top", margin));
        }
    }
    $(':input').ready(function() {
        for (var key in json.request) {
            if ($.query.get(key) && $.query.get(key) != "" && $.query.get(key) !== true) {
                var val = json.request[key];
                switch (key) {
                    case "from" :
                    case "to" :
                        val = new Date(val * 1000).toLocaleDateString();
                }
                $(':input[name="' + key + '"]').val(val);
            }
        }
    });
}
