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