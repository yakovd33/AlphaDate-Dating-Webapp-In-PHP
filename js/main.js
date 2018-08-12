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

// Sidebar prefrences bar
$("#hot-or-not-nav-link-options").click(function (e) {
    e.preventDefault();

    $("#change-prefrences-bar").fadeToggle(100);
    // $("#change-prefrences-bar").css('left', $(this).offset().left - ($("#change-prefrences-bar").width() / 2) + 5);
    $("#change-prefrences-bar").css('top', $(this).position().top + 42);
});

$("#update-prefrences-btn").click(function () {
    $("#change-prefrences-bar").fadeOut(100);

    data = new FormData();
    data.append('col', 'orientation');
    data.append('value', $("#orientation-selection .orientation-item.active").data('value'));
    
    $.ajax({
        url: URL + '/update_col/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            
            data = new FormData();
            data.append('col', 'interest_age_min');
            data.append('value', $('#slider-range').slider("values")[0]);
            
            $.ajax({
                url: URL + '/update_col/',
                processData: false,
                contentType: false,
                method : 'POST',
                data : data,
                success: function (response) {
                    console.log(response);
                    
                    data = new FormData();
                    data.append('col', 'interest_age_max');
                    data.append('value', $('#slider-range').slider("values")[1]);
                    
                    $.ajax({
                        url: URL + '/update_col/',
                        processData: false,
                        contentType: false,
                        method : 'POST',
                        data : data,
                        success: function (response) {
                            console.log(response);
                            next_hon();
                        }
                    });
                }
            });
        }
    });
});

$.each($("#orientation-selection .orientation-item"), function () {
    $(this).click(function () {
        $("#orientation-selection .orientation-item.active").removeClass("active");
        $(this).addClass("active");
    });
});

// Story
$("#sidebar-story-add-btn").click(function () {
    $("#popups-bg").fadeIn(150);

    setTimeout(function () {
        $("#new-story-adder-wrap").fadeIn(150);
    }, 150);
});

$("#popups-bg").click(function () {
    $("#new-story-adder-wrap").fadeOut(150);
});

$("#new-story-choose-pic").click(function () {
    $("#new-story-image-input").click();
});

$("#new-story-image-input").change(function () {
    if ($(this)[0].files.length > 0) {
        $("#new-story-pic img").remove();

        $("#new-story-text-wrap").show();

        var file = $($(this))[0].files[0];
        var reader = new FileReader();

        reader.addEventListener("load", function () {
            $("#new-story-pic").append('<img width="100%" src="' + reader.result + '">')
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    } else {
        $("#new-story-pic img").remove();
    }
});

$("#new-story-text-input").unbind("keyup").keyup(function () {
    $("#new-story-pic-text").text($(this).val());
});

$("#new-story-text-color-input").change(function () {
    if ($("#new-story-text-type-checkbox").prop('checked') == true) {
        $("#new-story-pic-text").css('color', '#fff');
        $("#new-story-pic-text").css('background-color', $("#new-story-text-color-input").val());
    } else {
        $("#new-story-pic-text").css('color', $("#new-story-text-color-input").val());
        $("#new-story-pic-text").css('background-color', 'transparent');
    }
});

$("#choose-text-type").click(function () {
    $("#new-story-text-type-checkbox").prop('checked', !($("#new-story-text-type-checkbox").prop('checked')));

    if ($("#new-story-text-type-checkbox").prop('checked') == true) {
        $("#new-story-pic-text").css('color', '#fff');
        $("#new-story-pic-text").css('background-color', $("#new-story-text-color-input").val());
    } else {
        $("#new-story-pic-text").css('color', $("#new-story-text-color-input").val());
        $("#new-story-pic-text").css('background-color', 'transparent');
    }
});

$("#submit-new-story").click(function () {
    if ($("#new-story-image-input")[0].files.length > 0) {
        img = $("#new-story-image-input")[0].files[0];
        text = $("#new-story-text-input").val();
        color = $("#new-story-text-color-input").val();
        isBg = $("#new-story-text-type-checkbox").prop('checked');

        data = new FormData();
        data.append('img', img);
        data.append('text', text);
        data.append('color', color);
        data.append('isBg', isBg);
        
        $.ajax({
            url: URL + '/story/upload/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            success: function (response) {
                console.log(response);
                $("#popups-bg").click();
            }
        });
    }
});

// Open stories
function open_stories () {
    $.each($(".story-list .item"), function () {
        $(this).click(function () {
            $("#popups-bg").fadeIn(150);

            setTimeout(function () {
                $("#story-showcase-wrap").fadeIn(150);
            }, 150);

            $("#story-user-profile-link").attr('href', URL + '/profile/' + $(this).data('userid') + '/');

            // Get user stories            
            $.ajax({
                url: URL + '/story/get_user_stories/' + $(this).data('userid') + '/',
                processData: false,
                contentType: false,
                method : 'GET',
                success: function (response) {
                    console.log(response);
                    response_json = JSON.parse(response);

                    $("#story-showcase-user-dets .fullname").text(response_json.fullname);
                    $("#story-showcase-user-dets .pp img").attr('src', response_json.pp);

                    $("#story-hourglasses").html('');

                    for (i = 0; i < response_json.stories.length; i++) {
                        $("#story-hourglasses").append('<span class="story-hourglass" data-storyid="' + response_json.stories[i].id + '"><span class="story-hourglass-spent"></span></span>');
                    }

                    for (i = 0; i < response_json.stories.length; i++) {
                        setTimeout(function (i, URL, response_json) {
                            $("#story-hourglasses .story-hourglass").eq(i).addClass("active");

                            $.ajax({
                                url: URL + '/story/get/' + $("#story-hourglasses .story-hourglass").eq(i).data('storyid'),
                                processData: false,
                                contentType: false,
                                method : 'GET',
                                success: function (response) {
                                    console.log(response);
                                    response_json = JSON.parse(response);
                                    $("#story-showcase-user-dets .time").text(response_json.time);
                                    $("#story-pic").css('background-image', 'url(' + 'data:image/png;base64,' + response_json.img + ')');

                                    $("#story-pic-text").text(response_json.text);
                                    
                                    if (response_json.isBg) {
                                        $("#story-pic-text").css('color', '#fff').css('background-color', response_json.color);
                                    }
                                }
                            });
                        }, 10000 * i, i, URL, response_json);
                    }

                    setTimeout(function () {
                        $("#popups-bg").click();
                    }, 10000 * response_json.stories.length);
                }
            });
        });
    });
}

open_stories();

$("#popups-bg").click(function () {
    $("#popups-bg").fadeOut(150);

    setTimeout(function () {
        $("#story-showcase-wrap").fadeOut(150);
    }, 150);
});

$("#empty-nav-mobile-menu-toggler").unbind("click").click(function() {
    $(this).toggleClass("toggled");

    $("#right-sidebar-wrap").slideToggle();
    $("#mobile-story").slideToggle();
});