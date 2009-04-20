if (typeof paulisageek == "undefined") { paulisageek = {}; }
if (typeof paulisageek.wg == "undefined") { paulisageek.wg = {}; }

paulisageek.wg.preGraphCallback = function(json) {
    $("#plot").height($(window).height() - $("#header").height() - $(".content h1").height() - 28);
};

paulisageek.wg.postGraphCallback = function(json) {
    $("#graphinfo").click(function() {
        $("#data").toggle("normal");
        return false;
    });

    var config = {
        showOn: 'button', 
        buttonImage: 'images/calendar.gif', 
        buttonImageOnly: true,
        onClose : function() { $("form#dateRange").submit(); }
    };
    $(":input[name='to']").datepicker(config);
    $(":input[name='from']").datepicker(config);
    $(":input[name='from']").change(function() {
        $(":input[name='days']").val("");
    });
    $(":input[name='days']").change(function() {
        $(":input[name='from']").val("");
    });

    if ($.isArray($.query.get("id")))
        $.query.SET("id", $.query.get("id").join(","));

    var keys = $.query.get();
    for (var keyName in keys) {
        if (keys[keyName] === true) {
            delete keys[keyName];
        }
    }
    var query = $.param(keys);

    json.graphs.push({ "meta" : {
        "API" : "ajax/v1/graph?" + query,
        "embed" : "",
        "derivative" : 'Show ' + ($.query.get("derivative") ? '<a href="' + $.query.remove("derivative").toString() + "&derivative=" + ($.query.get("derivative") + 1) + '">next derivative</a>' : '<a href="?' + query + '&derivative=1">derivative</a>') + ' graph (how the <b>change</b> changes over time)'
    }});
    var tbody = $(document.createElement("tbody"));
    $("#data").append($(document.createElement("table")).append(tbody)).hide();

    for (var graphIndex in json.graphs) {
        var graph = json.graphs[graphIndex];

        var meta = graph.meta;
        if (graph.meta.id)
            meta.extend = '<a href="createGraph?parent=' + meta.id + '">Extend</a> - Creates a new graph with the same history as this one. Good for fixing typos or broken xpath.';
        if (meta.badFetches >= 100 && (meta.goodFetches / (meta.goodFetches + meta.badFetches)) <= 0.25)
            meta.fetchingErrors = '<span class="error">This graph is not fetching because there are too many errors. <a href="createGraph?parent=' + meta.id + '">Extend</a> it and fix it or email me if think it should be fetching.</span>';


        for (var key in meta) {
            var tr = $(document.createElement("tr"));
            var th = $(document.createElement("th"));
            th.text(key).wrapInner("<b></b>");
            tr.append(th);
            var td = $(document.createElement("td"));
            td.text(meta[key]);
            switch (key) {
                case "openid" : 
                case "url" : 
                    if (meta[key] === "")
                        continue;
                    // Create an "a" around the element with the same content as the element
                    td.wrapInner($(document.createElement("a")).attr("href", td.text()));
                    break;
                case "API" :
                    var a = $(document.createElement("a")).attr("href", td.text());
                    var s = [];
                    for (var name in $.query.get()) {
                        if ($.query.get(name) !== true) {
                            s.push(name + "=" + $.query.get(name));
                        }
                    }
                    td.text("ajax/v1/graph?" + s.join("&"));
                    td.wrapInner(a);
                    break;
                case "embed" :
                    var iframe = $(document.createElement("iframe"))
                        .attr("frameborder", "0")
                        .attr("src", "http://paulisageek.com/webGraphr/embedGraph?type=js&" + query)
                        .css("width", "450px")
                        .css("height", "300px");
                    var input = $(document.createElement("input"))
                        .click(function() {this.focus(); this.select(); })
                        .attr("value", $(document.createElement("div")).append(iframe).html())
                        .attr("size", 90);
                    td.append(input);
                    var embedA = $(document.createElement("a"))
                        .attr("href",  "#")
                        .click(function() {
                            window.open('embedGraph?type=js&' + query, 'webGraphrPreview', 'width=450px, height=300px'); 
                            return false;
                        })
                        .text("Preview");
                    td.append(" ");
                    td.append(embedA);
                    break;
                case "xpath" :
                    td.wrapInner($(document.createElement("a")).attr("href", 'selectNode?' + $.param({url: meta.url, xpath: meta.xpath})));
                    break;
                case "createdTime" :
                case "modifiedTime" :
                    td.text(new Date(meta[key] * 1000).toString());
                    break;
                case "extend" :
                case "derivative" :
                case "fetchingErrors" :
                    td.html(td.text());
                    break;
            }
            tr.append(td);
            tbody.append(tr);
        }

        tbody.append("<tr><td>&nbsp;</td><td/></tr>");
    }

    $(':input').ready(function() {
        for (var key in json.request) {
            if ($.query.get(key) && $.query.get(key) !== "" && $.query.get(key) !== true) {
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
};
