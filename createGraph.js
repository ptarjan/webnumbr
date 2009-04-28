$(document).ready(function() {
    var messages = function() { 
        $("#messages").empty();
        var xpath = $(":input[name='xpath']").val();
        if (xpath.indexOf("tbody") != -1) {
            $("#messages").append("<span class='error'>tbody is often not in the document. Try removing it if you are seeing errors</span>");
        }
    }

    function reload() {
        $("#data").html('<img src="http://l.yimg.com/a/i/eu/sch/smd/busy_twirl2_1.gif" alt="thinking"/>');
        $.get("selectNode?" + $.param({url : $(":input[name='url']").attr("value"), xpath : $(":input[name='xpath']").attr("value"), action : "run" }), function (data) {
            $("#data").html(data);
        });
        messages();
    }
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
 
    var confirmed = false;
    $("form").submit(function(ev) {
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
        $("#dialog").dialog("open");
        if (!confirmed) {
            return false;
        } else {
            return true;
        }
    });
    $("#dialog").dialog({
        modal : true,
        autoOpen : false,
        buttons : {
            "Yes!" : function() { 
                confirmed = true;
                $("form").submit() 
            },
            "No! (Go back and edit)" : function () { $("#dialog").dialog("close"); }
        },
        title : "Last step before graphyness",
        "hide" : "slide"
    });
});
