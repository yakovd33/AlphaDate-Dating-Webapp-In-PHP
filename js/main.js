$("#floating-chat-toggler").click(function () {
    $("#chat-wrap").toggleClass("open", 500);
});

// Handlebars helpers
Handlebars.registerHelper('safe', function(text) {
    return new Handlebars.SafeString(text);
});

Handlebars.registerHelper('random_emoji', function () {
    emojis = [ 'smile-wink', 'smile-beam', 'smile', 'grin-beam-sweat', 'grin-squint-tears', 'grin-squint', 'grin-hearts', 'grin-beam', 'grin-alt', 'grin', 'grin-wink' ];
    return emojis[Math.floor(Math.random() * emojis.length)]
});

Handlebars.registerPartial('chat_message', $("#chat-message-template").html());

// Send flowers
function send_flowers () {
    $.each($(".send-flower"), function () {
        $(this).unbind("click").click(function () {
            data = new FormData();
            data.append('userid', $(this).data('userid'));
            
            $.ajax({
                url: URL + '/send_flower/',
                processData: false,
                contentType: false,
                method : 'POST',
                data : data,
                success: function (response) {
                    console.log(response);
                    toastr.success('פרח נשלח בהצלחה!', '');
                    if ($("#sidebar-flower-counter .num").length > 0) {
                        $("#sidebar-flower-counter .num").html(parseInt($("#sidebar-flower-counter .num").html()) - 1);
                    }
                }
            });
        });
    });
}

send_flowers();

// Date invites
function date_invites () {
    $.each($(".date-invitation-trigger"), function () {
        $(this).click(function () {
            data = new FormData();
            data.append('userid', $(this).data('userid'));
            
            $.ajax({
                url: URL + '/date/invite/',
                processData: false,
                contentType: false,
                method : 'POST',
                data : data,
                context: this,
                success: function (response) {
                    console.log(response);
                    $(this).remove();
                }
            });
        });
    });
}

date_invites();