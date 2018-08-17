blop = new Audio(URL + '/audio/blop.mp3');
fullscreen_chat_id = false;

// Socket.IO
// window.socket = io.connect('http://'+document.domain+':2021', { query: "userid=" + USERID });

socket = {
    connected: false
};

// window.socket = io.connect('http://192.168.43.20:2021', { query: "userid=" + USERID });

function socketing () {
    if (socket.connected) {
        $.each($("#chat-boxes .chat-box"), function () {
            socket.on('typing', function (from_id) { 
                // Show typing indicator
                $(".chat-box[data-userid='" + from_id + "'] .typing-indicator-wrap").show();
                setTimeout(function () {
                    $(".chat-box[data-userid='" + from_id + "'] .typing-indicator-wrap").show();
                }, 100);
                $(".chat-box[data-userid='" + from_id + "'] .chat-content-wrap").scrollTop($(".chat-box[data-userid='" + from_id + "'] .chat-content-wrap").prop('scrollHeight'));
            });

            socket.on('untyping', function (from_id) {    
                // Show typing indicator
                $(".chat-box[data-userid='" + from_id + "'] .typing-indicator-wrap").hide();
            });
        });
    }
}

function chat_togglers () {
    $.each($(".chatbox-trigger"), function () {
        $(this).unbind("click").click(function (e) {
            current_unread_messages -= parseInt($(this).find(".chat-list-unread-msgs-marker").text());
            $('body').append('<style>#floating-chat-toggler:after { content: "' + current_unread_messages + '"; } </style>');
            $(this).find(".chat-list-unread-msgs-marker").text('0');
            hide_read_markers();

            e.preventDefault();
            
            if ($(this).data('userid') != '') {
                read_private_messages($(this).data('userid'));

                if ($(window).width() <= 768) {
                    location.href = URL + '/conversation/' + $(this).data('userid') + '/';
                }

                // Check if chatbox is open
                if ($(".chat-box[data-userid='" + $(this).data('userid') + "']").length == 0) {
                    open_chatbox($(this).data('userid'));
                } else {
                    // Make chatbox visible and main
                    $.each($(".chat-box"), function () {
                        $(this).css('order', '1');
                    });

                    $(".chat-box[data-userid='" + $(this).data('userid') + "']").show().css('order', '1').find(".chatbox-wrap").show().find(".new-message ").click();
                }
            } else if ($(this).data('groupid') != '') {
                read_group_messages($(this).data('groupid'));

                if ($(window).width() <= 768) {
                    location.href = URL + '/conversation/group/' + $(this).data('groupid') + '/';
                }

                if ($(".chat-box[data-groupid='" + $(this).data('groupid') + "']").length == 0) {
                    open_group_chatbox($(this).data('groupid'));
                } else {
                    // Make chatbox visible and main
                    $.each($(".chat-box"), function () {
                        $(this).css('order', '1');
                    });

                    $(".chat-box[data-groupid='" + $(this).data('groupid') + "']").show().css('order', '1').find(".chatbox-wrap").show();
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
            var context = { userid: response_parsed.userid, fullname: response_parsed.fullname, messages: response_parsed.messages, isFolded: false, isLogged: response_parsed.isLogged, style: 'display: none' };
            var html = template(context);
            $("#chat-boxes").prepend(html);
            chatbox_options();

            $(".chat-box[data-userid='" + response_parsed.userid + "']").slideDown(250);

            // Scroll down in new chatbox
            $(".chat-content-wrap:first-child").scrollTop($(".chat-content-wrap:first-child").prop('scrollHeight'));
        }
    });
}

function open_group_chatbox (groupid) {
    chatbox_data = new FormData;
    chatbox_data.append('groupid', groupid);

    $.ajax({
        url: URL + '/get_group_chatbox/' + groupid,
        processData: false,
        contentType: false,
        method: "POST",
        data: chatbox_data,
        success: function (response) {
            console.log(response);

            if ($("#chat-boxes .chat-box").length >= 3) {
                // Delete first chatbox
                $("#chat-boxes .chat-box:last-child").remove();
            }

            console.log(response);
            response_parsed = JSON.parse(response);
            var source = $("#chatbox-template").html();
            var template = Handlebars.compile(source);
            console.log(response_parsed.messages);
            var context = { groupid: response_parsed.groupid, group_name: response_parsed.name, messages: response_parsed.messages, isFolded: false, isLogged: false, style: 'display: none' };
            var html = template(context);
            $("#chat-boxes").prepend(html);
            chatbox_options();

            $(".chat-box[data-groupid='" + response_parsed.groupid + "']").slideDown(250);

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

                    if ($(this).attr('data-userid') != "") {
                        data.append('userid', $(this).data('userid'));
                    } else if ($(this).attr('data-groupid') != "") {
                        data.append('groupid', $(this).data('groupid'));
                    }
                    
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
            if ($(this).data('userid') != '') {
                $.ajax({
                    url: URL + '/close_chatbox/' + $(this).data('userid'),
                    processData: false,
                    contentType: false,
                    method : 'POST',
                    context: this,
                    success: function (response) {
                        $(this).parent().parent().parent().slideUp(250);

                        setTimeout(function (context) {
                            $(context).parent().parent().parent().remove();
                        }, 250, this);
                    }
                });
            } else {
                $.ajax({
                    url: URL + '/close_chatbox/group/' + $(this).data('groupid'),
                    processData: false,
                    contentType: false,
                    method : 'POST',
                    context: this,
                    success: function (response) {
                        $(this).parent().parent().parent().slideUp(250);

                        setTimeout(function (context) {
                            $(context).parent().parent().parent().remove();
                        }, 250, this);
                    }
                });
            }
        });
    });

    $.each($(".name-options"), function () {
        $(this).unbind("click").click(function (e) {
            if ($(e.target).hasClass("name-options")) {
                $(this).parent().find(".chatbox-wrap").toggle();

                if ($(this).data('userid') != '') {
                    $.ajax({
                        url: URL + '/fold_chatbox/' + $(this).parent().data('userid'),
                        processData: false,
                        contentType: false,
                        method : 'POST',
                        context: this,
                        success: function (response) {
                        }
                    });
                } else if ($(this).data('groupid') != '') {
                    $.ajax({
                        url: URL + '/fold_chatbox/group/' + $(this).parent().data('groupid'),
                        processData: false,
                        contentType: false,
                        method : 'POST',
                        context: this,
                        success: function (response) {
                        }
                    });
                }
            }
        });
    })
}

// Scroll to bottom of chatboxes
$.each($(".chat-content-wrap"), function () {
	$(this).scrollTop($(this).prop('scrollHeight'));
});

// Listen to new messages
function message_listen () {
    listen_seconds = 0;

    setInterval(function (context) {
        // Prevents message from being sent right when loading
        // Listens for messages every 5 seconds if there are open chatboxes
        // Listens for messages every 22 seconds if there aren't open chatboxes
        
        if ((($(".chat-box").length > 0 && listen_seconds % 5 == 0) || ($(".chat-box").length == 0 && listen_seconds % 22 == 0)) && listen_seconds != 0) {
            console.log(listen_seconds);
            $.ajax({
                url: URL + '/messages_listen/',
                processData: false,
                contentType: false,
                method : 'POST',
                success: function (response) {
                    response_parsed = JSON.parse(response);

                    if (response_parsed.length > 0) {
                        blop.play();

                        current_unread_messages += response_parsed.length;
                        $('body').append('<style>#floating-chat-toggler:after { content: "' + current_unread_messages + '"; } </style>');
                    }

                    for (i = 0; i < response_parsed.length; i++) {
                        userid = response_parsed[i].userid;
                        text = response_parsed[i].text;
                        image = response_parsed[i].image;
                        date = response_parsed[i].date;

                        if (userid) {
                            // Private message
                            $(".chatbox-trigger[data-userid=" + userid + "]").find(".chat-list-unread-msgs-marker").text(parseInt($(".chatbox-trigger[data-userid=" + userid + "]").find(".chat-list-unread-msgs-marker").text()) + 1);

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

                            // Reorder chatlist
                            if ($("#floating-chat-connected-users .item[data-userid='" + userid + "']").length > 0) {
                                element = $("#floating-chat-connected-users .item[data-userid='" + userid + "']").clone(true);
                                $("#floating-chat-connected-users .item[data-userid='" + userid + "']").remove();
                                $("#floating-chat-connected-users").prepend(element);
                            } else {
                                // Add item to chatlist
                                data = new FormData();
                                data.append('userid', userid);
                                
                                $.ajax({
                                    url: URL + '/get_user_chatlist_item/',
                                    processData: false,
                                    contentType: false,
                                    method : 'POST',
                                    data : data,
                                    success: function (response) {
                                        console.log(response);
                                        response_parsed = JSON.parse(response);

                                        
                                        var source = $("#connected-users-list-template").html();
                                        var template = Handlebars.compile(source);
                                        var context = {
                                            fullname: response_parsed.fullname,
                                            userid: userid,
                                            pp: response_parsed.pp,
                                            city: response_parsed.city,
                                            unread_messages: response_parsed.unread_messages
                                        };
                                        var html = template(context);

                                        $("#floating-chat-connected-users").prepend(html);
                                        chat_togglers();
                                    }
                                });
                            }
                        } else {
                            // Group message
                            groupid = response_parsed[i].groupid;
                            fullname = response_parsed[i].fullname;
                            group_userid = response_parsed[i].group_userid;
                            
                            $(".chatbox-trigger[data-groupid=" + groupid + "]").find(".chat-list-unread-msgs-marker").text(parseInt($(".chatbox-trigger[data-groupid=" + groupid + "]").find(".chat-list-unread-msgs-marker").text()) + 1);
                            
                            if ($(".chat-box[data-groupid='" + groupid + "']").length == 0) {
                                // Chatbox isn't open
                                open_group_chatbox(groupid);
                            } else {
                                $(".chat-box[data-groupid='" + groupid + "']").show().css('order', '1').find(".chatbox-wrap").show();

                                var source = $("#chat-message-template").html();
                                var template = Handlebars.compile(source);
                                var context = {
                                    message: {
                                        fullname: fullname,
                                        userid: group_userid,
                                        text: text,
                                        isSelf: false,
                                        image: image,
                                        date: date
                                    }
                                };
                                var html = template(context);

                                $(".chat-box[data-groupid='" + groupid + "'] .chat-content-wrap .messages").append(html);

                                $(".chat-box[data-groupid='" + groupid + "'] .chat-content-wrap").scrollTop($(".chat-box[data-groupid='" + groupid + "'] .chat-content-wrap").prop('scrollHeight'));
                            }

                            if ($("#floating-chat-connected-users .item[data-groupid='" + groupid + "']").length > 0) {
                                element = $("#floating-chat-connected-users .item[data-groupid='" + groupid + "']").clone(true);
                                $("#floating-chat-connected-users .item[data-groupid='" + groupid + "']").remove();
                                $("#floating-chat-connected-users").prepend(element);
                            } else {
                                // Add item to chatlist
                                data = new FormData();
                                data.append('groupid', groupid);
                                
                                $.ajax({
                                    url: URL + '/get_group_chatlist_item/',
                                    processData: false,
                                    contentType: false,
                                    method : 'POST',
                                    data : data,
                                    success: function (response) {
                                        console.log(response);
                                        response_parsed = JSON.parse(response);

                                        
                                        var source = $("#connected-users-list-template").html();
                                        var template = Handlebars.compile(source);
                                        var context = {
                                            fullname: response_parsed.fullname,
                                            groupid: groupid,
                                            pp: response_parsed.pp,
                                            unread_messages: response_parsed.unread_messages
                                        };
                                        var html = template(context);

                                        $("#floating-chat-connected-users").prepend(html);
                                        chat_togglers();
                                    }
                                });
                            }
                        }
                    }

                    hide_read_markers();
                }
            });            
        }

        listen_seconds += 1;
    }, 1000, this);
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
    chat_image_upload();
    read_messages();

    if (socket.connected) {
        typing_option(window.socket);
        socketing();
    }
    
    $.each($(".new-message"), function () {
        $(this).unbind("click").click(function () {
            $(this).find(".new-message-input").focus();
        });
    });
}

// For fullscreen chat
if (!is_fullscreen_chat) {
    chatbox_options();
}

// Typing...
socketing();
function typing_option (socket) {
    if (socket.connected) {
        $.each($(".chat-box"), function () {
            if ($(this).data('userid') != "") {
                console.log('userrr');
                $(this).find(".new-message-input").unbind('keyup').keyup(function (e) {
                    console.log(e);
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
            }
        });
        }
}

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

            if ($(this).attr('data-userid') != "") {
                data.append('userid', $(this).data('userid'));
            } else if ($(this).attr('data-groupid') != "") {
                data.append('groupid', $(this).data('groupid'));
            }

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

// Create new group
$("#chat-new-group").click(function () {
    $("#popups-bg").fadeIn(150);

    setTimeout(function () {
        $("#new-group-popup").fadeIn(150);
    }, 150);
});

$("#popups-bg").click(function () {
    $("#popups-bg").fadeOut(150);

    setTimeout(function () {
        $("#new-group-popup").fadeOut(150);
    }, 150);
});

$.each($("#new-group-popup-members-select-list .member"), function () {
    $(this).unbind("click").click(function (e) {
        if ($(e.target).prop("tagName") != 'INPUT') {
            $(this).toggleClass("active");
        }
    });
});

$("#new-group-popup").submit(function (e) {
    e.preventDefault();

    if ($("#new-group-name").val().length > 0 && $("input[name='group_members[]']:checked").length > 1) {
        $.post(URL + '/new_group/', $(this).serialize(), function (group_id) {
            $("#popups-bg").click();

            // Add group to chat list
            $("#floating-chat-connected-users").prepend('<div class="item chatbox-trigger" data-groupid="' + group_id + '"> <div class="pic"><img src="/AlphaDate/img/icons/group-icon.png" alt=""></div> <div class="textual"> <div class="fullname"> ' + $("#new-group-name").val() + ' </div> </div> </div>');
            chatbox_options();
            open_group_chatbox(group_id);
        });
    }
});

// Read messages
$.each($(".new-message-input"), function () {
    $(this).focus(function () {
        if ($(this).data('userid') != '') {
            // Private message
            current_unread_messages -= parseInt($("#floating-chat-connected-users .item[data-userid='" + $(this).data('userid') + "'] .chat-list-unread-msgs-marker").text());
            $("#floating-chat-connected-users .item[data-userid='" + $(this).data('userid') + "'] .chat-list-unread-msgs-marker").text('0');
        } else {
            // Group message
            current_unread_messages -= parseInt($("#floating-chat-connected-users .item[data-groupid='" + $(this).data('groupid') + "'] .chat-list-unread-msgs-marker").text());
            $("#floating-chat-connected-users .item[data-groupid='" + $(this).data('groupid') + "'] .chat-list-unread-msgs-marker").text('0');
        }

        hide_read_markers();

        $('body').append('<style>#floating-chat-toggler:after { content: "' + current_unread_messages + '"; } </style>');
    });
});

function hide_read_markers () {
    $.each($(".chat-list-unread-msgs-marker"), function () {
        if (parseInt($(this).text()) == 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    })
}

hide_read_markers();

function read_messages () {
    $.each($(".new-message-input"), function () {
        $(this).focus(function () {
            if ($(this).data('userid') != '') {
                // Private message
                read_private_messages($(this).data('userid'));
            } else {
                // Group message
                read_group_messages($(this).data('groupid'));
            }
        })
    });
}

function read_private_messages (userid) {
    $.ajax({
        url: URL + '/conversation/read/' + userid + '/',
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
        }
    });
}

function read_group_messages (groupid) {
    $.ajax({
        url: URL + '/conversation/read/group/' + groupid + '/',
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
        }
    });
}

$("#floating-chat-toggler").click(function () {
    if ($(window).width() >= 768) {
        $("#chat-wrap").toggleClass("open", 500);
    } else {
        $(this).toggleClass("open", 500);
        $("#floating-chat-connected-users").toggleClass("open", 500);

        if (!$("#floating-chat-connected-users").hasClass("open")) {
            position_chat_toggler();
        }
    }
});

// Make mobile chat list toggler moveable on mobile
function position_chat_toggler () {
    if ($(window).width() <= 768) {
        if (getCookie('mobileChatTogPosX') && getCookie('mobileChatTogPosY')) {
            x = getCookie('mobileChatTogPosX');
            y = getCookie('mobileChatTogPosY');
        } else {
            x = $(window).width() - 50 - 10;
            y = $(window).height() - 50 - 10;
        }

        if (x > $(window).width() - 50 - 10) {
            x = $(window).width() - 50 - 10;
        }

        if (x < 10) {
            x = 10;
        }

        if (y > $(window).height() - 50 - 10) {
            y = $(window).height() - 50 - 10;
        }

        if (y < 10) {
            y = 10;
        }

        setCookie('mobileChatTogPosX', x);
        setCookie('mobileChatTogPosY', y);

        $("#floating-chat-toggler").css('top', y + 'px').css('left', x + 'px');
    }
}

position_chat_toggler();

$(window).resize(position_chat_toggler);

var t;
$(document).on('touchstart mousedown','#floating-chat-toggler', function (event) {
  var self = this;
  if ($(self).hasClass('draggable')) return;
  t = setTimeout(function () {
    $(self).draggable({
        scroll: false,
        stop: function (event, ui) {
            $('body, html').css('overflow', 'auto');

            var x = ui.position.left;
            var y = ui.position.top;

            setCookie('mobileChatTogPosX', x);
            setCookie('mobileChatTogPosY', y);

            clearTimeout(t);
        }, drag: function(event, ui) {
            $('body, html').css('overflow', 'hidden');

            var leftPosition = ui.position.left;
            var topPosition = ui.position.top;

            if (leftPosition > $(window).width() - 50 - 10) {
                ui.position.left = $(window).width() - 50 - 10;
            }

            if (leftPosition < 10) {
                ui.position.left = 10;
            }

            if (topPosition > $(window).height() - 50 - 10) {
                ui.position.top = $(window).height() - 50 - 10;
            }

            if (topPosition < 10) {
                ui.position.top = 10;
            }
        }
    }).draggable('enable').addClass('draggable');
    $(self).trigger(event)
  }, 800);
});

$(document).on("touchend mouseup", function () {
  clearTimeout(t);
  $('.draggable').draggable( 'disable' ).removeClass('draggable');
});