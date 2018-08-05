<!DOCTYPE html>
<html lang="en" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Alpha Date</title>
        <script src="<?php echo $URL; ?>/js/handlebars-v4.0.11.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo $URL; ?>/css/main.css">

        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <!-- Slick JS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
    </head>
    <body>
    <!-- <div style="width: 300px; height: 472px !important; background: #fcb555; z-index: 999; position: fixed; bottom: -200px; left: -200px; transform: rotate(130deg);"><div class="text"></div></div> -->
        <input type="hidden" id="url" value="<?php echo $URL; ?>">
        <?php if (is_logged()) : ?>
            <input type="hidden" id="userid" value="<?php echo $_SESSION['user_id']; ?>">
        <?php endif; ?>
        <input type="hidden" id="fullname" value="<?php echo $CUR_USER['fullname']; ?>">

        <script>
            URL = $("#url").val();
            USERID = $("#userid").val();
            FULLNAME = $("#fullname").val();
        </script>

        <?php if (isset($_GET['page']) && $_GET['page'] != 'profile' || !isset($_GET['page'])) : ?>
            <div class="container" id="site-wrap">
        <?php endif; ?>