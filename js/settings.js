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