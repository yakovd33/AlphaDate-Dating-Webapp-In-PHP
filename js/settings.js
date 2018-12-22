$("#password-reset-ajax").click(function (e) {
    e.preventDefault();

    data = new FormData();
    data.append('user_id', USERID);
    
    $.ajax({
        url: URL + '/reset_password/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            $("#password-reset-feedback").fadeIn();
        }
    });
});

$("#change-password-wrap").submit(function (e) {
    e.preventDefault();

    if ($("#new-pass").val().length > 0 && $("#new-pass-rep").val().length > 0 && $("#old-pass").val().length > 0) {
        if ($("#new-pass").val() == $("#new-pass-rep").val()) {
            data = new FormData();
            data.append('old-pass', $("#old-pass").val());
            data.append('new-pass', $("#new-pass").val());
            
            $.ajax({
                url: URL + '/user/change_password/',
                processData: false,
                contentType: false,
                method : 'POST',
                data : data,
                success: function (response) {
                    console.log(response);
                    $("#change-password-wrap").text(response);
                }
            });
        } else {
            $("#password-change-feedback").text('על הסיסמאות להיות שוות.');
        }
    } else {
        $("#password-change-feedback").text('על כל השדות להיות מלאים.');
    }
});