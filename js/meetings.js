$.each($(".approve-date"), function () {
    $(this).click(function (e) {
        data = new FormData();
        data.append('dateid', $(this).data('dateid'));
        
        $.ajax({
            url: URL + '/dates/approve/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            context: this,
            success: function (response) {
                console.log(response);
                $(this).parent().parent().parent().fadeOut();
            }
        });
    });
});

$.each($(".reject-date"), function () {
    $(this).click(function (e) {
        data = new FormData();
        data.append('dateid', $(this).data('dateid'));
        
        $.ajax({
            url: URL + '/dates/reject/',
            processData: false,
            contentType: false,
            method : 'POST',
            data : data,
            context: this,
            success: function (response) {
                console.log(response);
                $(this).parent().parent().parent().fadeOut();
            }
        });
    });
});