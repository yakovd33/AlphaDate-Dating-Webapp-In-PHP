<div id="hot-or-not-section">
    <div id="current-hot-or-not-profile">
        <?php
            if ($CUR_USER['is_in_hot_or_not']) {
                require_once('includes/hor-functions.php');
                $hor = get_hon();

                if ($hor != 'not found') {
                    $hor = json_decode($hor, true);
                    echo $handlebars->render("hot-or-not-item", [
                        'userid'=> $hor['userid'],
                        'fullname'=> $hor['fullname'],
                        'age'=> $hor['age'],
                        'city'=> $hor['city'],
                        'gender' => $hor['gender'],
                        'popularity'=> $hor['popularity'],
                        'num_images'=> $hor['num_images'],
                        'images'=> $hor['images'],
                        'pp'=> $hor['pp'],
                    ]);
                } else {
                    echo '<div class="hor-title">לא נמצאו עוד משתמשים התואמים את הנתונים שהזנת.<br>נסה שנית מאוחר יותר</div>';
                    echo '<img src="' . $URL . '/img/icons/sad-love.png" height="120px" style="display: block; margin: 30px auto auto auto;">';
                }
            }
        ?>
    </div>

    <?php if (!$CUR_USER['is_in_hot_or_not']) : ?>
        <div id="hon-not-activated-wrap">
            <div id="hon-pre-join-msg">על מנת להצטרף לאיזור ההיכרויות עלייך להשלים את הפרופיל ולהוסיף לפחות תמונה אחת</div>

            <?php if (get_user_num_hon_pics($_SESSION['user_id']) == 0 && is_cur_user_profile_complete()) : ?>
                <div id="hot-or-not-image-adder-toggle"></div>
                <input type="file" id="hot-or-not-image-adder-input" accept="image/x-png,,image/jpeg" style="display: none">
            <?php endif; ?>

            <?php if (is_cur_user_profile_complete()) : ?>
                <button class="cute-btn" id="hon-join-btn" style="<?php if (is_cur_user_profile_complete() && get_user_num_hon_pics($_SESSION['user_id']) > 0) { echo 'display: block'; } ?>">כניסה לאיזור ההיכרויות</button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($CUR_USER['is_in_hot_or_not']) : ?>
    <button class="cute-btn" id="hon-pics-selector-trigger"><i class="fas fa-camera"></i> בחר תמונות שיוצגו</button>
<?php endif; ?>

<div id="hon-pics-selector">
    <div id="hon-image-deletion-msg">לחיצה על תמונה תמחק אותה</div>

    <?php
        $user_hon_pics_stmt = $GLOBALS['link']->query("SELECT * FROM `hot_or_not_pics` WHERE `user_id` = {$_SESSION['user_id']}");
    ?>

    <div id="hon-pics-selector-pics">
        <?php while ($pic = $user_hon_pics_stmt->fetch()) : ?>
            <img class="hon-pic-selector-pic-item" data-picid="<?php echo $pic['id']; ?>" src="<?php echo get_image_path_by_id($pic['image_id']); ?>" alt="">
        <?php endwhile; ?>
    </div>

    <div id="hon-pics-selector-order-message">*סדר התמונות ייבחר אוטומטית לפי התמונות שיביאו לכם הכי הרבה התאמות</div>

    <button id="add-image" class="cute-btn" style="float: right; <?php if (get_user_num_hon_pics($_SESSION['user_id']) >= 6) { echo 'display: none;'; } ?>"><i style="margin-left: 5px;" class="fas fa-camera"></i> <?php echo genderize_text('הוסף'); ?> תמונה</button>
    <button id="close-hon-pics-selector" class="cute-btn mr-auto" style="background: #fff; color: #c03b2b; display: block;">סגור</button>
    <input type="file" accept="image/x-png,,image/jpeg" id="hon-pic-selector-new-pic">
</div>

<script id="hon-template" type="text/x-handlebars-template">
    <?php include 'templates/hot-or-not-item.hbs'; ?>
</script>

<script src="<?php echo $URL; ?>/js/hot-or-not.js"></script>