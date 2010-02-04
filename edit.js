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

    var checkName = function(node, callback) {
        if ($(":input[name='name'][type='text']").attr('disabled')) {
            if (typeof callback == "function")
                callback();
            return
        }

        var val = node.val();
        var msg = $("#name_msg");
        /*
        if (val != "" && val == node.attr("defaultValue")) {
            msg.html('<span style="color: green">Good old name</span>');
            return;
        }
        */
        val = val.toLowerCase();
        val = val.replace(/[^a-z0-9-]/g, '-'); 
        node.val(val);
        // msg.html('<img src="http://l.yimg.com/a/i/eu/sch/smd/busy_twirl2_1.gif" alt="thinking"/> Validating...');
        $.get("checkName?" + $.param({name: node.val()}), function (data) {
            if (! data)
                msg.html('<span style="color: green">Your number will be at </span> http://webnumbr.com/' + val);
            else
                msg.html('<span style="color: red">' + data + '</span>');
            if (typeof callback == "function")
                callback();
        });
    };
    $(":input[name='name']").blur(function() {
            checkName($(this))
    });
    if ($(":input[name='name']").val()) $(":input[name='name']").blur();

    var updateName = function() {
        if ($(":input[name='name'][type='text']").attr('disabled')) return;

        var val = $(this).val();
        val = val.toLowerCase().replace(/[^a-z0-9-]/g, '-'); 
        $(":input[name='name']").val(val);
        checkName($(":input[name='name']"));
    }
    $(":input[name='title']").keyup(updateName);

    var reload = function() {
        $("#data").html('<img src="/images/twirl.gif" alt="thinking"/>');
        $.get("create?" + $.param({url : $(":input[name='url']").attr("value"), xpath : $(":input[name='xpath']").attr("value"), action : "run" }), function (data) {
            $("#data").html(data.replace("Fetch Exception: ", ""));
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
        if (validate.bypass)
            return true;

        validate.bypass = false;

        $(".error").each(function() {
            $(this).replaceWith($(this).contents());
        });
        $(":input[name='title']").keyup();
        checkName($(":input[name='name']"), function() {

            var good = true;

            var data = parseInt($("#data").text());
            if (isNaN(data)) {
                $("#data").wrapInner("<span class='error' style='color:red'></span>");
                good = false;
            }

            if (! $(":input[name='name'][type='text']").attr('disabled')) {
                if ($("#name_msg span").text() == "" || $("#name_msg span").css("color") !== "green") {
                    $("#name_msg").wrap("<span class='error'></span>").focus();
                    good = false;
                }
            }

            // $("#dialog").dialog("open");
            if (good) {
                validate.bypass = true;
                $("form.edit-form").submit();
            }

        });
        return false;
    }
    $("form.edit-form").submit(validate);

    /*
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
    */

    $("#advanced_toggle").toggle(function() {
        $("#advanced").show();
    }, function() {
        $("#advanced").hide();
    });
});

});
