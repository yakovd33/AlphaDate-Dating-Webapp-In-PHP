$("#new-post-input-card-content").focus(function () {
    $("#main-right-sidebar-profile-card-visual-post-input").show();
    $("#main-right-sidebar-profile-card-visual-post-input").css('display', 'flex');
    $("#new-post-bottom").css('display', 'flex');
    $(this).addClass("focus")
});

// Post upload
$("#post-upload-form").submit(function (e) {
    e.preventDefault();

    data = new FormData();
    data.append('text', $("#new-post-input-card-content").val());
    
    $.ajax({
        url: URL + '/post/upload/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        context: this,
        success: function (response) {
            parsed_response = JSON.parse(response);

            var source = $("#post-template").html();
            var template = Handlebars.compile(source);
            var context = { postid: parsed_response.postid, userid: USERID, fullname: FULLNAME, time: 'עכשיו', text: $("#new-post-input-card-content").val().replace(/\r?\n/g, '<br>'), num_hearts: 0, num_comments: 0, hearted: false };
            var html = template(context);
            $("#feed-posts").prepend(html);            
            $("#new-post-input-card-content").val('');

            heart();
            posts_comments();
        }
    });
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
           $(this).parent().parent().find(".post-comments-section").toggle();    
        });
    })
}

posts_comments();