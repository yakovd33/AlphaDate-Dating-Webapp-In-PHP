// Handlebars helpers
Handlebars.registerHelper('safe', function(text) {
    return new Handlebars.SafeString(text);
});

Handlebars.registerHelper('random_emoji', function () {
    emojis = [ 'smile-wink', 'smile-beam', 'smile', 'grin-beam-sweat', 'grin-squint-tears', 'grin-squint', 'grin-hearts', 'grin-beam', 'grin-alt', 'grin', 'grin-wink' ];
    return emojis[Math.floor(Math.random() * emojis.length)]
});

Handlebars.registerPartial('chat_message', $("#chat-message-template").html());

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

var hexDigits = new Array ("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); 

//Function to convert rgb color to hex format
function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
}

function hex(x) {
    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
}

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

    if ($(document).width() > 768) {
        $("#change-prefrences-bar").fadeToggle(100);
        // $("#change-prefrences-bar").css('left', $(this).offset().left - ($("#change-prefrences-bar").width() / 2) + 5);
        $("#change-prefrences-bar").css('top', $(this).position().top + 42);
    } else {
        $("#popups-bg").fadeIn(150);

        setTimeout(function () {
            $("#change-prefrences-bar").fadeIn(100);
        }, 150);
    }
});

$("#popups-bg").click(function () {
    $("#popups-bg").fadeOut(150);

    setTimeout(function () {
        $("#change-prefrences-bar").fadeOut(100);
    }, 150);
});

$("#update-prefrences-btn").click(function () {
    $("#popups-bg").click();

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
    $("#new-story-image-input").click();
    $("#popups-bg").fadeIn(150);

    setTimeout(function () {
        $("#new-story-adder-wrap").fadeIn(150);
    }, 150);
});

$("#sidebar-story-add-btn-mobile").click(function () {
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
        $("#new-story-pic-disclamer").show();

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
        $("#new-story-pic-disclamer").hide();
    }
});

$("#new-story-text-input").unbind("keyup").keyup(function () {
    $("#new-story-pic-text").text($(this).val());
});

$("#new-story-text-color-input").change(function () {
    color_story_text();
});

$("#choose-text-type").click(function () {
    $("#new-story-text-type-checkbox").prop('checked', !($("#new-story-text-type-checkbox").prop('checked')));

    color_story_text();
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
        data.append('csrf_token', $("#csrf_token").val());
        
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
                        story_views = 0;

                        if (response_json.stories[i].is_self) {
                            story_views = response_json.stories[i].story_views;
                        }

                        $("#story-hourglasses").append('<span class="story-hourglass" data-storyid="' + response_json.stories[i].id + '" data-isself="' + response_json.stories[i].is_self + '" data-storyviews="' + story_views + '"><span class="story-hourglass-spent"></span></span>');
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

                                    if (response_json.is_self) {
                                        $("#story-num-views-num").text(response_json.story_views);
                                        $("#story-num-views").show();
                                    } else {
                                        $("#story-num-views").hide();
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

$.each($("#new-story-text-colors .color"), function () {
    $(this).click(function () {
        if ($(this).attr('id') != 'new-story-rand-color') {
            $("#new-story-text-color-input").val(rgb2hex($(this).css('background-color')));
        } else {
            $("#new-story-text-color-input").val(getRandomColor());
        }

        color_story_text(); 
    });
})

function color_story_text() {
    if ($("#new-story-text-type-checkbox").prop('checked') == true) {
        $("#new-story-pic-text").css('color', '#fff');
        $("#new-story-pic-text").css('background-color', $("#new-story-text-color-input").val());
    } else {
        $("#new-story-pic-text").css('color', $("#new-story-text-color-input").val());
        $("#new-story-pic-text").css('background-color', 'transparent');
    }
}

// $(window).scroll(function () {
//     if (isMobileFloatingChat) {
//         if ($(window).scrollTop() >= $("#empty-nav").outerHeight()) {
//             $("#mobile-story").css('position', 'fixed');
//         } else {
//             $("#mobile-story").css('position', 'relative');
//         }
//     }
// });

function delete_hobby (hobby_id) {
    data = new FormData();
    data.append('hobby_id', hobby_id);
    
    $.ajax({
        url: URL + '/hobby/delete/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            
        }
    });
}