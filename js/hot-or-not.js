$("#hot-or-not-image-adder-toggle").click(function () {
    $("#hot-or-not-image-adder-input").click();
});

$("#hot-or-not-image-adder-input").change(function (e) {
    if ($(this).length == 1) {
        data = new FormData();
        data.append('pic', $($(this))[0].files[0]);
        
        $.ajax({
            url: URL + '/upload_hon_pic/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            context: this,
            success: function (response) {
                console.log(response);
                $("#hon-join-btn").fadeIn(200).css('display', 'block');
            }
        });
    }
});

$("#hon-join-btn").click(function () {
    $.ajax({
        url: URL + '/user_join_hon/',
        processData: false,
        contentType: false,
        method : 'POST',
        success: function (response) {
            console.log(response);
            // location.reload();
            $("#hon-not-activated-wrap").remove();
            next_hon();
        }
    });
});

function next_hon () {
    $.ajax({
        url: URL + '/get_next_hon/',
        processData: false,
        contentType: false,
        method : 'POST',
        success: function (response) {
            console.log(response);

            if (response != 'not found') {
                response_parsed = JSON.parse(response);

                var source = $("#hon-template").html();
                var template = Handlebars.compile(source);
                var context = {
                    userid: response_parsed.userid,
                    fullname: response_parsed.fullname,
                    age: response_parsed.age,
                    city: response_parsed.city,
                    popularity: response_parsed.popularity,
                    num_images: response_parsed.num_images,
                    images: response_parsed.images,
                };

                var html = template(context);
            } else {
                var html = '<div class="hor-title">לא נמצאו עוד משתמשים התואמים את הנתונים שהזנת.<br>נסה שנית מאוחר יותר</div><img src="/AlphaDate/img/icons/sad-love.png" height="120px" style="display: block; margin: 30px auto auto auto;">';
            }

            $("#current-hot-or-not-profile").html(html);
            hon_actions();
        }
    });
}

// Heart hon
function hon_actions () {
    $("#current-hot-or-not-heart").click(function () {
        $.ajax({
            url: URL + '/hon/heart/',
            processData: false,
            contentType: false,
            method : 'POST',
            success: function (response) {
                console.log(response);
                next_hon();
            }
        });
    });

    // Reject hon
    $("#current-hot-or-not-pass").click(function () {
        $.ajax({
            url: URL + '/hon/reject/',
            processData: false,
            contentType: false,
            method : 'POST',
            success: function (response) {
                console.log(response);
                next_hon();
            }
        });
    });

    // Gallery navigation
    $("#hon-gallery-right-arrow").click(function () {
        $("#current-hot-or-not-profile-gallery-pics");
        $("#current-hot-or-not-profile-gallery-pics").scrollLeft($("#current-hot-or-not-profile-gallery-pics").scrollLeft() + $("#current-hot-or-not-profile-gallery-pics").width())
    })

    $("#hon-gallery-left-arrow").click(function () {
        $("#current-hot-or-not-profile-gallery-pics");
        $("#current-hot-or-not-profile-gallery-pics").scrollLeft($("#current-hot-or-not-profile-gallery-pics").scrollLeft() - $("#current-hot-or-not-profile-gallery-pics").width())
    })
}

hon_actions();

// Pics manager
$("#hon-pics-selector-trigger").click(function () {
    $("#popups-bg").fadeIn(150);

    setTimeout(function () {
        $("#hon-pics-selector").show(150);
    }, 150);
});

$("#add-image").click(function () {
    $("#hon-pic-selector-new-pic").click();
});

$("#hon-pic-selector-new-pic").change(function () {
    if ($("#hon-pic-selector-new-pic")[0].files.length > 0) {
        data = new FormData();
        data.append('pic', $($(this))[0].files[0]);
        
        $.ajax({
            url: URL + '/upload_hon_pic/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            context: this,
            success: function (response) {
                var file = $($(this))[0].files[0];
                var reader = new FileReader();

                reader.addEventListener("load", function () {
                    $("#hon-pics-selector-pics").append('<img class="hon-pic-selector-pic-item" src="' + reader.result + '">')
                }, false);

                if (file) {
                    reader.readAsDataURL(file);
                }

                if ($(".hon-pic-selector-pic-item").length >= 6) {
                    $("#add-image").hide();
                }

                $(this).val('');
                delete_hon_pics();
            }
        });
    }
});

$("#close-hon-pics-selector").click(function () {
    $("#popups-bg").fadeOut(150);

    setTimeout(function () {
        $("#hon-pics-selector").hide(150);
    }, 150);
});

$("#popups-bg").click(function () {
    $("#popups-bg").fadeOut(150);

    setTimeout(function () {
        $("#hon-pics-selector").hide(150);
    }, 150);
});

function delete_hon_pics () {
    $.each($(".hon-pic-selector-pic-item"), function () {
        $(this).unbind("click").click(function () {
            data = new FormData();
            data.append('picid', $(this).data('picid'));
            
            $.ajax({
                url: URL + '/delete_hon_pic/',
                processData: false,
                contentType: false,
                method : 'POST',
                data : data,
                success: function (response) {
                    console.log(response);
                    
                }
            });

            $(this).fadeOut(550);
            
            setTimeout(function (context) {
                context.remove();

                if ($(".hon-pic-selector-pic-item").length < 6) {
                    $("#add-image").css('display', 'inline-block');
                    document.getElementById("close-hon-pics-selector").style['margin-right'] = 'calc(100% - 40px) !important';
                }
            }, 550, this);
            
            delete_hon_pics();
        });
    });
}

delete_hon_pics();