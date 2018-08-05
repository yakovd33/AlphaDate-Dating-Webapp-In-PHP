blop = new Audio(URL + '/audio/blop.mp3');

function chat_togglers () {
    $.each($(".chatbox-trigger"), function () {
        $(this).unbind("click").click(function (e) {
            e.preventDefault();
            
            if ($(this).data('userid') != undefined) {
                // Check if chatbox is open
                if ($(".chat-box[data-userid='" + $(this).data('userid') + "']").length == 0) {
                    open_chatbox($(this).data('userid'));
                } else {
                    // Make chatbox visible and main
                    $.each($(".chat-box"), function () {
                        $(this).css('order', '1');
                    });

                    $(".chat-box[data-userid='" + $(this).data('userid') + "']").show().css('order', '1').find(".chatbox-wrap").show();
                }
            }
        });
    })
}

function open_chatbox (userid) {
    chatbox_data = new FormData;
    chatbox_data.append('userid', userid);

    $.ajax({
        url: URL + '/get_checkbox/' + userid,
        processData: false,
        contentType: false,
        method: "POST",
        data: chatbox_data,
        success: function (response) {
            if ($("#chat-boxes .chat-box").length >= 3) {
                // Delete first chatbox
                $("#chat-boxes .chat-box:last-child").remove();
            }

            console.log(response);
            response_parsed = JSON.parse(response);
            var source = $("#chatbox-template").html();
            var template = Handlebars.compile(source);
            console.log(response_parsed.messages);
            var context = { userid: response_parsed.userid, fullname: response_parsed.fullname, messages: response_parsed.messages, isFolded: false, isLogged: response_parsed.isLogged };
            var html = template(context);
            $("#chat-boxes").prepend(html);
            chatbox_options();

            // Scroll down in new chatbox
            $(".chat-content-wrap:first-child").scrollTop($(".chat-content-wrap:first-child").prop('scrollHeight'));
        }
    });
}

// Send message
function sendMessages () {
    $.each($(".new-message-input"), function () {
        $(this).unbind("keydown").keydown(function (e) {
            if (e.which == 13) {
                if ($(this).html().length > 0) {
                    data = new FormData();
                    data.append('text', $(this).html());
                    data.append('userid', $(this).data('userid'));
                    
                    $.ajax({
                        url: URL + '/send_message/',
                        processData: false,
                        contentType: false,
                        method : 'POST',
                        data : data,
                        context: this,
                        success: function (response) {
                            console.log(response);
                            
                            var source = $("#chat-message-template").html();
                            var template = Handlebars.compile(source);
                            var context = { message: {
                                text: $(this).html(),
                                isSelf: true,
                                time: 'עכשיו'
                            } };
                            var html = template(context);
                            $(this).parent().parent().find(".chat-content-wrap .messages").append(html);

                            $(this).html('');

                            $(this).parent().parent().find(".chat-content-wrap").scrollTop($(this).parent().parent().find(".chat-content-wrap").prop('scrollHeight'));
                        }
                    });
                }           
            }
        })
    })
}

// Close chatbox
function close_chatboxes () {
    $.each($(".close-chatbox"), function () {
        $(this).unbind("click").click(function () {
            $.ajax({
                url: URL + '/close_chatbox/' + $(this).data('userid'),
                processData: false,
                contentType: false,
                method : 'POST',
                context: this,
                success: function (response) {
                    $(this).parent().parent().parent().remove();
                }
            });
        });
    });

    $.each($(".name-options"), function () {
        $(this).unbind("click").click(function () {
            $(this).parent().find(".chatbox-wrap").toggle();

            $.ajax({
                url: URL + '/fold_chatbox/' + $(this).parent().data('userid'),
                processData: false,
                contentType: false,
                method : 'POST',
                context: this,
                success: function (response) {
                }
            });
        });
    })
}

// Scroll to bottom of chatboxes
$.each($(".chat-content-wrap"), function () {
	$(this).scrollTop($(this).prop('scrollHeight'));
});

// Listen to new messages
function message_listen () {
    setInterval(function (context) {
        $.ajax({
            url: URL + '/messages_listen/',
            processData: false,
            contentType: false,
            method : 'POST',
            success: function (response) {
                console.log(response);
                response_parsed = JSON.parse(response);

                if (response_parsed.length > 0) {
                    blop.play();
                }

                for (i = 0; i < response_parsed.length; i++) {
                    console.log(response_parsed[i].userid);

                    userid = response_parsed[i].userid;
                    text = response_parsed[i].text;
                    image = response_parsed[i].image;
                    date = response_parsed[i].date;
                    console.log('image: ' + image);

                    if ($(".chat-box[data-userid='" + userid + "']").length == 0) {
                        // Chatbox isn't open
                        open_chatbox(userid);
                    } else {
                        $(".chat-box[data-userid='" + userid + "']").show().css('order', '1').find(".chatbox-wrap").show();

                        var source = $("#chat-message-template").html();
                        var template = Handlebars.compile(source);
                        var context = {
                            message: {
                                text: text,
                                isSelf: false,
                                image: image,
                                date: date
                            }
                        };
                        var html = template(context);

                        $(".chat-box[data-userid='" + userid + "'] .chat-content-wrap .messages").append(html);

                        $(".chat-box[data-userid='" + userid + "'] .chat-content-wrap").scrollTop($(".chat-box[data-userid='" + userid + "'] .chat-content-wrap").prop('scrollHeight'));
                    }
                }
            }
        });
    }, 5000, this);
}

// Fold on esc
function fold_on_esc () {
    $.each($(".chat-box"), function () {
        $(this).keydown(function (e) {
            if (e.which == 27) {
                $(this).find(".chatbox-wrap").hide();

                $.ajax({
                    url: URL + '/fold_chatbox/' + $(this).data('userid'),
                    processData: false,
                    contentType: false,
                    method : 'POST',
                    context: this,
                    success: function (response) {
                    }
                });
            }
        })
    })
}

function chatbox_options () {
    chat_togglers();
    sendMessages();
    close_chatboxes();
    message_listen();
    fold_on_esc();
    typing_option();
    chat_image_upload();
    
    $.each($(".new-message"), function () {
        $(this).unbind("click").click(function () {
            $(this).find(".new-message-input").focus();
        });
    });
}

chatbox_options();

// Socket.IO
if (false) {
    var socket = io.connect('http://'+document.domain+':2021', { query: "userid=" + USERID });

    $.each($(".chat-box"), function () {
        socket.on('typing', function (from_id) {       
            // Show typing indicator
            $(".chat-box[data-userid='" + from_id + "'] .typing-indicator-wrap").show();
            $(".chat-box[data-userid='" + from_id + "'] .chat-content-wrap").scrollTop($(".chat-box[data-userid='" + from_id + "'] .chat-content-wrap").prop('scrollHeight'));
        });

        socket.on('untyping', function (from_id) {    
            // Show typing indicator
            $(".chat-box[data-userid='" + from_id + "'] .typing-indicator-wrap").hide();
        });
    });
}

// Typing...
function typing_option (socket) {
    $.each($(".chat-box"), function () {
        $(this).find(".new-message-input").unbind('keyup').keyup(function (e) {
            if ($(this).html().length == 1 && !$(this).hasClass("typing")) {
                $(this).addClass("typing");
                socket.emit('typing', USERID + ';' + $(this).data('userid'));
            }

            // Untype
            if ($(this).html().length == 0) {
                $(this).removeClass("typing");
                socket.emit('untyping', USERID + ';' + $(this).data('userid'));
            }
        });
    })
}

typing_option(socket);

function chat_image_upload () {
    $.each($(".trigger-message-additional-image"), function () {
        $(this).click(function () {
            $(this).parent().find(".message-additional-image").click();
        });
    });
    $.each($(".message-additional-image"), function () {
        $(this).unbind('change').change(function () {
            data = new FormData();
            data.append('text', '');
            data.append('userid', $(this).data('userid'));
            data.append('pic', $($(this))[0].files[0]);
            
            $.ajax({
                url: URL + '/send_message/',
                processData: false,
                contentType: false,
                method : 'POST',
                data : data,
                context: this,
                success: function (response) {
                    console.log(response);
                    response_parsed = JSON.parse(response);

                    var source = $("#chat-message-template").html();
                    var template = Handlebars.compile(source);
                    var context = {
                        message: {
                            text: $(this).html(),
                            isSelf: true,
                            date: 'עכשיו',
                            image: response_parsed.image
                        }
                    };

                    var html = template(context);
                    $(this).parent().parent().parent().find(".chat-content-wrap .messages").append(html);
                    $(this).val();
                    $(this).parent().parent().parent().find(".chat-content-wrap").scrollTop($(this).parent().parent().parent().find(".chat-content-wrap").prop('scrollHeight'));
                }
            });
        });
    });
}