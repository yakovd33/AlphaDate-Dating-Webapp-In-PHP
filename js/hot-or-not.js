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