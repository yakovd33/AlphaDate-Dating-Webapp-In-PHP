$("#new-post-input-card-content").focus(function () {
    $("#main-right-sidebar-profile-card-visual-post-input").show();
    $("#main-right-sidebar-profile-card-visual-post-input").css('display', 'flex');
    $("#new-post-bottom").css('display', 'flex');
    $(this).addClass("focus")
});

// Post anonymous icon
$("#new-post-anonymous").click(function () {
    $(this).parent().find("#main-right-sidebar-profile-card-textuals .nickname").toggle();
    $(this).parent().find("#main-right-sidebar-profile-card-textuals .fullname").toggle();
    $(this).parent().find("#main-right-sidebar-profile-card-textuals .fullname.anonymous").focus();
    $(this).parent().find(".new-post-pic").toggle();
    $("#new-post-is-anonymous").prop('checked', !$("#new-post-is-anonymous").prop('checked'));
});

// Post upload
$("#post-upload-form").submit(function (e) {
    e.preventDefault();

    data = new FormData();
    data.append('text', $("#new-post-input-card-content").val());
    data.append('is_anonymous', $("#new-post-is-anonymous").prop('checked'));
    data.append('anonymous_nickname', $("#main-right-sidebar-profile-card-textuals .fullname.anonymous").text());
    data.append('csrf_token', $("#csrf_token").val());

    if ($("#new-post-pic-input")[0].files.length > 0) {
        data.append('image', $("#new-post-pic-input")[0].files[0]);
    }
    
    if ($("#new-post-input-card-content").val().length > 0 || $("#new-post-pic-input")[0].files.length > 0) {
        $.ajax({
            url: URL + '/post/upload/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            context: this,
            success: function (response) {
                console.log(response);
                parsed_response = JSON.parse(response);

                new_post_fullname = FULLNAME;
                user_pic = PP;
                anonymous = '';
                
                if ($("#new-post-is-anonymous").prop('checked')) {
                    new_post_fullname = $("#main-right-sidebar-profile-card-textuals .fullname.anonymous").text();
                    user_pic = URL + '/img/anonymous.png';
                    anonymous = 'anonymous';
                }

                if ($("#new-post-pic-input")[0].files.length > 0) {

                }

                var source = $("#post-template").html();
                var template = Handlebars.compile(source);
                var context = {
                    postid: parsed_response.postid,
                    userid: USERID,
                    fullname: new_post_fullname,
                    time: 'עכשיו',
                    text: $("#new-post-input-card-content").val().replace(/\r?\n/g, '<br>'),
                    num_hearts: 0,
                    num_comments: 0,
                    hearted: false,
                    user_pic: user_pic,
                    anonymous: anonymous
                };

                if ($("#new-post-pic-input")[0].files.length > 0) {
                    context.image = $("#new-post-pic-wrap img").attr('src');
                }
                
                var html = template(context);
                $("#feed-posts").prepend(html);            
                $("#new-post-input-card-content").val('');

                posts_comments();
            }
        });
    }
});

// Heart
function heart (context) {    
    $.ajax({
        url: URL + '/post/heart/' + $(context).data('postid'),
        processData: false,
        contentType: false,
        method : 'POST',
        context: context,
        success: function (response) {
            if ($(context).hasClass("clicked")) {
                $(context).find(".num").text(parseInt($(context).find(".num").text()) - 1);
            } else {
                $(context).find(".num").text(parseInt($(context).find(".num").text()) + 1);
            }

            $(context).toggleClass("clicked");
        }
    });
}

function posts_comments () {
    $.each($(".post-actions .comment"), function () {
        $(this).unbind("click").click(function () {
            if ($(this).parent().parent().find(".post-comments-section").css('display') == 'none') {
                // Load post comments
                data = new FormData();
                data.append('postid', $(this).data('postid'));
                
                $.ajax({
                    url: URL + '/post/get_comments/',
                    processData: false,
                    contentType: false,
                    method : 'POST',
                    data : data,
                    context: this,
                    success: function (response) {
                        console.log(response);
                        responseJson = JSON.parse(response);
                        
                        for (i = 0; i < responseJson.length; i++) {
                            var source = $("#post-comment-template").html();
                            var template = Handlebars.compile(source);
                            var context = {
                                fullname: responseJson[i].fullname,
                                pp: responseJson[i].pp,
                                text: responseJson[i].comment,
                            };

                            var html = template(context);
                            $(this).parent().parent().find(".post-comments-wrap").append(html);
                        }
                    }
                });
            }

            $(this).parent().parent().find(".post-comments-section").show().css('display', 'flex');
        });
    });

    $.each($(".new-comment-input"), function () {
        $(this).find(".comment-text-input").unbind("keydown").keydown(function (e) {
           if (e.which == 13) {
               data = new FormData();
               data.append('postid', $(this).data('postid'));
               data.append('comment', $(this).html());
               
                $.ajax({
                    url: URL + '/post/comment/',
                    processData: false,
                    contentType: false,
                    method : 'POST',
                    data : data,
                    context: this,
                    success: function (response) {
                        console.log(response);
                        responseJson = JSON.parse(response);

                        $(this).html('');

                        var source = $("#post-comment-template").html();
                        var template = Handlebars.compile(source);
                        var context = {
                            fullname: responseJson.fullname,
                            pp: responseJson.pp,
                            text: responseJson.comment,
                        };

                        var html = template(context);
                        $(this).parent().parent().parent().find(".post-comments-wrap").prepend(html);
                    }
                });
            }
        });
    });
}

posts_comments();

// Lazyload
$(window).scroll(function () {  
    if ($(window).scrollTop() >= $(document).height() - $(window).height()) {
        if (!hasFeedEnded) {
            feedPage++;

            if (isMainFeed) {
                $.ajax({
                    url: URL + '/get-main-feed-page/' + feedPage,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        console.log(response);
                        
                        response_json = JSON.parse(response);

                        if (response_json.posts.length < postsPerPage) {
                            // No more posts
                            hasFeedEnded = false;
                        }

                        for (i = 0; i < response_json.posts.length; i++) {
                            console.log(response_json.posts[i].postid);
                            var template = Handlebars.compile($('#post-template').html());
                            var context = {
                                postid: response_json.posts[i].postid,
                                userid: response_json.posts[i].userid,
                                fullname: response_json.posts[i].fullname,
                                text: response_json.posts[i].text,
                                time: response_json.posts[i].time,
                                num_hearts: response_json.posts[i].num_hearts,
                                num_comments: response_json.posts[i].num_comments,
                                hearted: response_json.posts[i].hearted,
                                user_pic: response_json.posts[i].user_pic
                            };
                            var html = template(context);
                            $("#feed-posts").append(html);
                        }
                    }
                });
            } else if (isProfileFeed) {
                data = new FormData();
                data.append('userid', profileid);

                $.ajax({
                    url: URL + '/get-main-feed-page/' + feedPage,
                    processData: false,
                    contentType: false,
                    data: data,
                    method: 'POST',
                    success: function (response) {
                        console.log(response);
                        
                        response_json = JSON.parse(response);

                        if (response_json.posts.length < postsPerPage) {
                            // No more posts
                            hasFeedEnded = false;
                        }

                        for (i = 0; i < response_json.posts.length; i++) {
                            console.log(response_json.posts[i].postid);
                            var template = Handlebars.compile($('#post-template').html());
                            var context = {
                                postid: response_json.posts[i].postid,
                                userid: response_json.posts[i].userid,
                                fullname: response_json.posts[i].fullname,
                                text: response_json.posts[i].text,
                                time: response_json.posts[i].time,
                                num_hearts: response_json.posts[i].num_hearts,
                                num_comments: response_json.posts[i].num_comments,
                                hearted: response_json.posts[i].hearted,
                                user_pic: response_json.posts[i].user_pic
                            };
                            var html = template(context);
                            $("#feed-posts").append(html);
                        }
                    }
                });
            }
        }
    }
});

// Change feed sorting
$.each($(".feed-sorting-option"), function () {
    $(this).click(function (e) {
        e.preventDefault();

        data = new FormData();
        data.append('sort', $(this).data('sort'));

        $(".feed-sorting-option.active").removeClass("active");
        $(this).addClass("active");
        
        $.ajax({
            url: URL + '/feed/sort/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            success: function (response) {
                console.log(response);
                location.reload();
            }
        });
    });
});

// New post pic
$("#new-post-pic-trigger").click(function () {
    $("#new-post-pic-input").click();
});

$("#new-post-pic-input").change(function () {
    if ($(this)[0].files.length > 0) {

        var file = $($(this))[0].files[0];
        var reader = new FileReader();

        reader.addEventListener("load", function () {
            $("#new-post-pic-wrap").append('<img src="' + reader.result + '">')
        }, false);

        if (file) {
            reader.readAsDataURL(file);
        }
    }
});