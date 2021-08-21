<link rel="stylesheet" href="<?php echo $URL; ?>/css/settings.css">

<a href="#" id="password-reset-ajax">שחזור סיסמא (אימייל יישלח לכתובת הרשומה)</a>
<div id="password-reset-feedback" style="display: none">אימייל לשחזור סיסמא נשלח אלייך לכתובת <?php echo $CUR_USER['email']; ?></div>

<!-- <form id="change-password-wrap">
    <label for="">שינוי סיסמא</label>
    <input type="password" class="cute-input" id="old-pass" name="old-pass" placeholder="סיסמא ישנה">
    <input type="password" class="cute-input" id="new-pass" name="new-pass" placeholder="סיסמא חדשה">
    <input type="password" class="cute-input" id="new-pass-rep" placeholder="חזור על הסיסמא בשנית">
    <div>
        <input type="submit" class="cute-btn" value="עדכן סיסמא">
    </div>

    <div id="password-change-feedback"></div>
</form> -->

<script src="<?php echo $URL; ?>/js/settings.js"></script>