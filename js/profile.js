// Profile editing

// About me edit
$("#about-me-wrap .editable-icon").click(function () {
    if ($(".about-me.editable").attr('contenteditable') == undefined) {
        $(".about-me.editable").attr('contenteditable', 'plaintext-only').focus();
    } else {
        $(".about-me.editable").removeAttr('contenteditable')
    }

    $("#about-me-update-btn").toggle();
});

$("#about-me-update-btn").click(function () {
    $(".about-me.editable").removeAttr('contenteditable')
    $(this).hide();

    data = new FormData();
    data.append('col', 'about_me');
    data.append('value',  $(".about-me.editable").html());
    data.append('csrf_token', $("#csrf_token").val());
    
    $.ajax({
        url: URL + '/update_col/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            $(".about-me.editable").html($(".about-me.editable").html().trim());
        }
    });
});

// Edit job
$("#profile-job-info-item .editable-icon").click(function () {
    $("#profile-job-info-item .editable-content").toggle();
});

$("#job-update-btn").click(function () {
    data = new FormData();
    data.append('col', 'profession');
    data.append('value',  $("#profession-input").val());
    
    $.ajax({
        url: URL + '/update_col/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);

            (function () {
                data = new FormData();
                data.append('col', 'company');
                data.append('value',  $("#company-input").val());
                
                $.ajax({
                    url: URL + '/update_col/',
                    processData: false,
                    contentType: false,
                    method : 'POST',
                    data : data,
                    success: function (response) {
                        console.log(response);
                        $("#profile-job-info-item .editable-content").hide();
                        $("#job-item-content").html('<strong>' + $("#profession-input").val() + '</strong> ' + $("#company-input").val());
                    }
                });
            })();
        }
    });
});

// Edit education
$("#profile-education-info-item .editable-icon").click(function () {
    $("#profile-education-info-item .editable-content").toggle();
});

$("#education-update-btn").click(function () {
    data = new FormData();
    data.append('col', 'education');
    data.append('value',  $("#education-input").val());
    
    $.ajax({
        url: URL + '/update_col/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            $("#profile-education-info-item .editable-content").hide();
            $("#education-item-content").html($("#education-input").val());
        }
    });
});

// Edit info
$("#profile-information-item-info .editable-icon").click(function () {
    $("#personal-info-list").toggle();
    $(this).parent().find(".editable-wrap").toggle();
});

$("#info-update-btn").click(function () {
    inputs = $("#profile-information-item-info .profile-info-edit-input");
    update_cols(inputs.length);
    $("#personal-info-list").show();
    $("#profile-information-item-info .editable-wrap").hide();

    // Show changes on page
    $("#user-fullname").html($("#fullname-input").val());
    
    $.each($(".profile-info-edit-input"), function () {
        console.log('#' + $(this).data('col') + '-det');
        $('#' + $(this).data('col') + '-det').html($(this).val());
    })
});

function update_cols (num) {
    if (num > 0) {
        input = $("#profile-information-item-info .profile-info-edit-input").eq(num - 1);
        data = new FormData();
        data.append('col', input.data('col'));
        data.append('value',  input.val());
        
        $.ajax({
            url: URL + '/update_col/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            success: function (response) {
                console.log(response);
                update_cols(num - 1);
            }
        });
    }
}

// Block user
$(".block-user").click(function () {
    data = new FormData();
    data.append('userid', $(this).data('userid'));
    
    $.ajax({
        url: URL + '/user/block/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            
        }
    });
});

// Change pp
$(".profile-card .pp.self").unbind("click").click(function () {
    $("#self-pp-changer-input")[0].click();
});

$("#self-pp-changer-input").change(function () {
    if ($(this)[0].files.length > 0) {
        data = new FormData();
        data.append('pic', $(this)[0].files[0]);
        
        $.ajax({
            url: URL + '/set-pp/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            success: function (response) {
                console.log(response);
                
            }
        });
    }
});

$.each($(".profile-pic"), function () {
    $(this).css('height', $(this).width())
});

// Make pp same height as width
$(".profile-card .pp img").height($(".profile-card .pp img").width());

// Add new hobbies
$("#new-hobby-btn").click(function () {
    $("#profile-hobbies").append('<div class="hobby-item"><div class="title" onkeydown="if (event.keyCode == 13) { event.preventDefault(); add_new_hobby($(this)); }" contenteditable="plaintext-only"></i></div> </div>');
    $("#profile-hobbies .hobby-item:last-child .title").focus();
});

function add_new_hobby (element) {
    console.log(element);

    data = new FormData();
    data.append('text', $(element).html());
    $(".hobby-item:last-child .title").removeAttr('contenteditable');
    
    $.ajax({
        url: URL + '/hobby/new/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            $(".hobby-item:last-child").click(function () {
                delete_hobby(response);
                $(this).remove();
            });
        }
    });
}