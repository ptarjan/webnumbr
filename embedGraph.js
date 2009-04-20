if (typeof paulisageek == "undefined") { paulisageek = {}; }
if (typeof paulisageek.wg == "undefined") { paulisageek.wg = {}; }

paulisageek.wg.preGraphCallback = function(json) {
    $("#title").ready(function() {
        var keys = $.query.keys;
        delete keys.type;
        if ($("#title").text().length > 50) {
            $("#title").text($("#title").text().substring(0,47) + "...");
        }
        $("#title").wrap('<a target="webGrapher" href="http://paulisageek.com/webGraphr/graph?' + $.param(keys) + '"></a>"');
    });
    $("#plot").height(($(".content").innerHeight() - $("#title").height() + 5));
    $("#plot").width(($(".content").width() - 10));
    $("#plot").css("margin", -10);
}
paulisageek.wg.postGraphCallback = function(json) {
    $(".legend a").attr("target", "webGrapher");
    var legend = $(".legend");
    var html = legend.html();
    legend.data("oldLegend", html);
    var div = $(".legend div:first");
    legend.html($("<a/>")
        .attr("href", "#")
        .append("Show Legend")
        .css({
            "top":div.css("top"),
            "left":div.css("left"),
            "position":div.css("position"),
            "margin-left":div.css("margin-left"),
            "margin-top":div.css("margin-top"),
            "color":"black"
        })
        .click(function() {
            legend.html(legend.data("oldLegend"));
        })
    );
}
