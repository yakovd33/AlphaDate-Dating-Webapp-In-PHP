$.each($("#membreship-popup-tabs-togglers .tab"), function () {
    $(this).click(function () {
        $("#membreship-popup-tabs-togglers .tab.active").removeClass("active");
        $(this).addClass("active");
        
        $("#membreship-popup-tabs .tab.active").removeClass("active");
        $("#membreship-popup-tabs .tab[data-tab='" + $(this).data('tab') + "']").addClass("active")
    })
});

$("#popups-wrap").click(function (e) {
    if (e.target.id == 'popups-wrap') {
        $(this).hide();
        $.each($("#popups-wrap .popup"), function () {
            $(this).hide();
        })
    }
});

$("#forgot-pass-btn").click(function (e) {
    e.preventDefault();
    $("#login-form").hide();
    $("#forgot-poassword-form").show();
    $("#membreship-popup-tabs-togglers .tab[data-tab='login']").text('איפוס סיסמא');
});

$("#close-forgot-pass-form").click(function (e) {
    e.preventDefault();
    $("#login-form").show();
    $("#forgot-poassword-form").hide();
    $("#membreship-popup-tabs-togglers .tab[data-tab='login']").text('יש לכם חשבון? התחברו');
});

// Ajax signup
$("#signup-form").submit(function (e) {
    e.preventDefault();

    $.post($(this).attr('action'), $(this).serialize(), function(response) {
        console.log(response);
        
        if (response != 'success') {
            $("#signup-form-feedback").show().text(response);
        } else {
            location.reload();
        }
    });
});