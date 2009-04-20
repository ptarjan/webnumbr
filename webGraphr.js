$('<div class="content"></div>')
.attr("id", "signin")
.css("position", "absolute")
.css("right", 0)
.css("padding", 10)
.css("margin", 10)
.append(
    $('<a href="#">Sign In</a>')
    .one("click", function() {
        $(this)
        .html('<form action="openid/try_auth"><div style="padding: 0.2em; text-align: center;"><input type="text" style="border: 1px solid rgb(119, 136, 153); padding: 0.2em 0.2em 0.2em 20px; background: #FFFFFF url(https://s.fsdn.com/sf/images//openid/openid_small_logo.png) no-repeat scroll 0 50%;" size="28" value="http://" class="openid_url_input" name="openid_identifier" id="openid_identifier"/></div></form>')
        ;
        return false;
    })
)
.prependTo($("body"))
