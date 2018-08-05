<?php
    $profiles_query = "SELECT * FROM `users` WHERE 1";

    if ($CUR_USER['orientation'] != 'both') { 
        $profiles_query .= " AND `gender` = '" . $CUR_USER['orientation'] . "'";
    }

    $profiles_query .= " AND (YEAR(`date_of_birth`) < YEAR(CURDATE()) - " . $CUR_USER['interest_age_min'] . ")";
    $profiles_query .= " AND (YEAR(`date_of_birth`) > YEAR(CURDATE()) - " . $CUR_USER['interest_age_max'] . ")";

    $profiles_stmt = $GLOBALS['link']->query($profiles_query);

    // $GLOBALS['link']->query("UPDATE `users` SET `about_me` = 'לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית לורם איפסום דולור סיט אמט, קונסקטורר אדיפיסינג אלית. סת אלמנקום ניסי נון ניבאה. דס איאקוליס וולופטה דיאם. וסטיבולום אט דולור, קראס אגת לקטוס וואל אאוגו וסטיבולום סוליסי טידום בעליק. קונדימנטום קורוס בליקרה, נונסטי קלובר בריקנה סטום, לפריקך תצטריק לרטי. '");
?>

<div id="profiles-tab">
    <div class="row">
        <?php while ($profile = $profiles_stmt->fetch()) : ?>
            <div class="col-md-4 profiles-tab-profile-wrap">
                <div class="profiles-tab-profile-card">
                    <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/">
                        <div class="pp" style="background-image: url(<?php echo $URL; ?>/img/pp.jpg);">
                                <img src="<?php echo $URL; ?>/img/pp.jpg" style="visibility: hidden">
                            <div class="send-message-btn chatbox-trigger" data-userid="<?php echo $profile['id']; ?>"><i class="fas fa-comment-alt"></i></div>
                        </div>
                    </a>

                    <div class="textual">
                        <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/">
                            <div class="fullname"><?php echo $profile['fullname']; ?></div>
                        </a>

                        <a href="<?php echo $URL; ?>/city/<?php echo $profile['city']; ?>/">
                            <div class="location"><?php echo $profile['city']; ?></div>
                        </a>

                        <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/">
                            <strong class="about-me-label">קצת עליי:</strong>
                            <p class="about-me">
                                <?php echo strlen($profile['about_me']) > 100 ? mb_substr($profile['about_me'], 0, 100)."..." : $profile['about_me']; ?>
                            </p>
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>