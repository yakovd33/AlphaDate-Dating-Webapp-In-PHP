$("#success-stories-slider").slick({
    arrows: false,
    dots: true,
    rtl: true,
    autoplay: true,
    autoplaySpeed: 3500,
});

$("#nav-hero-login-link").click(function () {
    $("#popups-wrap").show();
    $("#popups-wrap").css('display', 'flex');
    $("#membreship-popup").show();
    $("#membreship-popup-tabs-togglers .tab[data-tab='login']").click();
});

$("#forgot-poassword-form").submit(function (e) {
    e.preventDefault();

    data = new FormData();
    data.append('email', $("#password-reset-email-input").val());
    
    $.ajax({
        url: URL + '/reset_password/',
        processData: false,
        contentType: false,
        method : 'POST',
        data : data,
        success: function (response) {
            console.log(response);
            
        }
    });
});

// $("#password-reset-form-wrap").submit(function (e) {
//     e.preventDefault();


//     if ($("#password-reset-first-input").val().length > 0 && $("#password-reset-second-input").val().length > 0) {
//         if ($("#password-reset-first-input").val() == $("#password-reset-second-input").val()) {
//             data = new FormData();
//             data.append('password', $("#password-reset-first-input").val());
//             data.append('token', $("#password-reset-token").val());
            
//             $.ajax({
//                 url: URL + '/change_password/',
//                 processData: false,
//                 contentType: false,
//                 method : 'POST',
//                 data : data,
//                 success: function (response) {
//                     console.log(response);

//                     if (response == 'success') {
//                         location.href = URL + '/?password_changed';
//                     }
//                 }
//             });
//         } else {
//             $("#password-change-feedback").text('על הסיסמאות להיות זהות.');
//         }
//     } else {
//         $("#password-change-feedback").text('יש לוודא שאין שדות ריקים.');
//     }
// });

// $("#password-reset-bg").click(function () {
//     $("#password-reset-form-wrap").hide();
//     $(this).hide();
// });

$("#join-btn").click(function () {
    $("#hero-signup-card-content").hide(200);
    $("#signup-fields").show(200);
})

// Ajax signup
$("#signup-form-2").submit(function (e) {
    e.preventDefault();

    $.post($(this).attr('action'), $(this).serialize(), function(response) {
        console.log(response);
        
        if (response != 'success') {
            $("#signup-form-feedback-2").show().text(response);
        } else {
            location.reload();
        }
    });
});