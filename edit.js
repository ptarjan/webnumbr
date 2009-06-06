google.load("jquery", "1");
google.load("jqueryui", "1");
google.setOnLoadCallback(function() {

$(document).ready(function() {
    var messages = function() { 
        $("#messages").empty();
        var xpath = $(":input[name='xpath']").val();
        if (xpath.indexOf("tbody") != -1) {
            $("#messages").append("<span class='error'>tbody is often not in the document. Try removing it if you are seeing errors</span>");
        }
    }

    var checkName = function() {
        var val = $(this).val();
        var msg = $("#name_msg");
        if (val == $(this).attr("defaultValue")) {
            msg.html('<span style="color: green">Good Old Name</span>');
            return;
        }
        val = val.toLowerCase();
        val = val.replace(/[^a-z0-9-]/g, '-'); 
        $(this).val(val);
        msg.html('<img src="http://l.yimg.com/a/i/eu/sch/smd/busy_twirl2_1.gif" alt="thinking"/>');
        $.get("checkName?" + $.param({name: $(this).val()}), function (data) {
            if (! data)
                msg.html('<span style="color: green">Good Name!</span>');
            else
                msg.html('<span style="color: red">' + data + '</span>');
        });
    };
    $(":input[name='name']").blur(checkName);
    if ($(":input[name='name']").val()) $(":input[name='name']").blur();

    var reload = function() {
        $("#data").html('<img src="images/twirl.gif" alt="thinking"/>');
        $.get("create?" + $.param({url : $(":input[name='url']").attr("value"), xpath : $(":input[name='xpath']").attr("value"), action : "run" }), function (data) {
            $("#data").html(data);
        });
        messages();
    };
    $("#data").ready(reload);
    $("#reload").click(reload);
  
    $(":input[name='xpath']").keydown(function(event) {
        switch(event.keyCode) {
            case 13 :
                reload();
                return false;
                break;
        }
    });

    var validate = function(ev) { 
        $(".error").each(function() {
            $(this).replaceWith($(this).contents());
        });

        if ($.trim($(":input[name='name']").val()) == "") {
            $(":input[name='name']").wrap("<span class='error' style='border:10px solid red'></span>").focus();
            return false;
        }
        var data = parseInt($("#data").text());
        if (isNaN(data)) {
            $("#data").wrapInner("<span class='error' style='color:red'></span>");
            return false;
        }
        if ( $.trim($(":input[name='title']").val()) == "") {
            $(":input[name='title']").wrap("<span class='error' style='border:10px solid red'></span>").focus();
            return false;
        }

        if ($("#name_msg span").css("color") !== "green") {
            $("#name_msg").wrap("<span class='error' style='border:5px solid red'></span>").focus();
            return false;
        }

        $("#dialog").dialog("open");
        return false;
    }
    $("form").submit(validate);

    $("#dialog").dialog({
        modal : true,
        autoOpen : false,
        buttons : {
            "Yes!" : function() { 
                $("form").unbind("submit", validate);
                $("form").submit();
                $("#dialog").text("Submitting ... ");
                $("#dialog").dialog('option', 'buttons', {});
            },
            "No! (Go back and edit)" : function () { $("#dialog").dialog("close"); }
        },
        title : "Check your work",
    });
});

});
