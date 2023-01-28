<?php
    if (!is_logged()) {
        header("Location: " . $URL);
    }

    if (isset($_GET['id'], $_GET['profile_hash'])) {
        $id = $_GET['id'];
        $profile_hash = $_GET['profile_hash'];

        if ($GLOBALS['link']->query("SELECT * FROM `users` WHERE `id` = {$id} AND `profile_hash` = '{$profile_hash}'")->rowCount() == 0) {
            echo '<script>location.href = "' . $URL . '";</script>';
            die();
        }
    } else {
        $id = $CUR_USER['id'];
    }

    if (is_user_blocked($id)) {
        header("Location: " . $URL);
    }

    $user = get_user_row_by_id($id);
    $age = $GLOBALS['link']->query("SELECT YEAR(CURDATE()) - YEAR(`date_of_birth`) AS `age` FROM `users` WHERE `id` = {$id}")->fetch()['age'];

    if ($user['banned']) {
        echo '<script>location.href = "' . $URL . '";</script>';
        die();
    }
    
    if ($id != $_SESSION['user_id']) {
        // Add profile view
        //  Check if already viewed today
        if ($GLOBALS['link']->query("SELECT * FROM `profile_views` WHERE `viewer_id` = {$_SESSION['user_id']} AND `viewed_id` = {$id} AND `date` = CURDATE()")->rowCount() == 0) {
            // Insert view
            if (isset($_SERVER['HTTP_REFERER'])) {
                $refer = $_SERVER['HTTP_REFERER'];
            } else {
                $refer = '';
            }

            $GLOBALS['link']->query("INSERT INTO `profile_views`(`viewer_id`, `viewed_id`, `date`, `referal`) VALUES ({$_SESSION['user_id']}, {$id}, CURDATE(), '{$refer}')");
        
            // Increase viewed profile user popularity
            increase_user_popularity($id, 2);
        } else {
            // Increase streak
            $GLOBALS['link']->query("UPDATE `profile_views` SET `streak` = `streak` + 1 WHERE `viewer_id` = {$_SESSION['user_id']} AND `viewed_id` = {$id} AND `date` = CURDATE()");
        }
    }
?>

<link rel="stylesheet" href="<?php echo $URL; ?>/css/profile.css">

<?php include 'story-essantials.php'; ?>

<div class="col-md-3" id="right-sidebar-wrap">
    <?php include 'views/sidebars/right-sidebar.php'; ?>
</div>

<div class="container" id="site-wrap">
    <div class="row">
        <div class="col-md-3">
            <div class="profile-card">
                <div class="pp <?php if ((isset($_GET['id']) && $_GET['id'] == $_SESSION['user_id']) || !isset($_GET['id'])) { echo 'self'; } ?>">
                    <img src="<?php echo get_user_pp_by_id($id); ?>">
                    <?php if ($user['gender'] != null) : ?>
                        <div id="gender-marker" class="<?php echo $user['gender']; ?>" style="display: none"></div>
                    <?php endif; ?>

                    <input type="file" id="self-pp-changer-input" accept="image/x-png,,image/jpeg">
                </div>

                <div class="fullname"><span id="user-fullname"><?php echo $user['fullname']; ?></span>, <?php echo $age; ?></div>
                <div class="city"><?php echo $user['city']; ?></div>

                <?php if ($id != $_SESSION['user_id']) : ?>
                    <div id="profile-actions">
                        <div class="profile-action-wrap chatbox-trigger" data-userid="<?php echo $id; ?>">
                            <span class="right-side"></span>
                            <span class="icon"><i class="fas fa-comment-alt"></i></span>
                            <span class="text"><span><?php echo genderize_text('שלח'); ?> הודעה</span></span>
                        </div>

                        <?php if ($GLOBALS['link']->query("SELECT `flowers` FROM `users` WHERE `id` = {$_SESSION['user_id']}")->fetch()['flowers'] > 0) : ?>
                            <div class="profile-action-wrap send-flower" data-userid="<?php echo $id; ?>">
                                <span class="right-side"></span>
                                <span class="icon"><img src="<?php echo $URL; ?>/img/icons/flower-plus.svg" height="20px"></span>
                                <span class="text"><span><?php echo genderize_text('שלח'); ?> פרח</span></span>
                            </div>
                        <?php endif; ?>

                        <div class="profile-action-wrap block-user" data-userid="<?php echo $id; ?>">
                            <span class="right-side"></span>
                            <span class="icon"><i class="fas fa-ban"></i></span>
                            <span class="text"><span><?php echo genderize_text('חסום'); ?> משתמש</span></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="editable-wrap" id="about-me-wrap">
                    <h6 class="about-me-label">קצת עליי</h6>
                    <div class="editable-icon"></div>
                    <p class="about-me editable editable-content"><?php echo $user['about_me'] != null ? $user['about_me'] : genderize_text('כתוב') . ' משהו עלייך.'; ?></p>

                    <button id="about-me-update-btn" class="cute-btn"><?php echo $translate['update'][$CUR_USER['gender']]; ?></button>
                    <div class="clearfix"></div>
                </div>

                <div class="profile-information-item editable-wrap">
                    <div class="item-content">
                        <div id="profile-pics">
                            <?php $user_pics_stmt = $GLOBALS['link']->query("SELECT * FROM `images` WHERE `user_id` = {$id} AND NOT `is_pp` AND NOT `is_message` AND NOT `is_story` AND NOT `is_message` ORDER BY `date` DESC LIMIT 6"); ?>

                            <?php while ($pic = $user_pics_stmt->fetch()) : ?>
                                <img src="<?php echo $URL; ?>/<?php echo get_image_path_by_id($pic['id']); ?>" alt="" class="profile-pic">
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9" id="profile-content-wrap">
            <div class="row">
                <script>
                    let isMainFeed = false;
                    let isProfileFeed = true;
                    let feedPage = 0;
                    let hasFeedEnded = false;
                    let postsPerPage = <?php echo get_setting('posts_per_page'); ?>;
                    let profileid = <?php echo $id; ?>
                </script>

                <div class="col-md-8 feed-col order-md-1 order-2">
                    <?php
                        if ($id == $_SESSION['user_id']) {
                            echo $handlebars->render("new_post", [
                                'fullname' => $CUR_USER['fullname'],
                                'nickname' => $CUR_USER['nickname'],
                                'user_pic' => get_user_pp_by_id($_SESSION['user_id']),
                                'post_now_text' => $translate['post_now'][$CUR_USER['gender']],
                                'share_with_your_followers_text' => $translate['share_with_your_followers'][$CUR_USER['gender']]
                            ]);
                        }
                    ?>

                    <div id="feed-posts">
                        <?php
                            $posts_query = "SELECT * FROM `posts` WHERE `user_id` = {$id} AND NOT `is_anonymous` AND NOT `is_deleted` ORDER BY `date` DESC LIMIT " . get_setting('posts_per_page');
                            $posts_stmt = $GLOBALS['link']->query($posts_query);
                        ?>

                        <?php
                            while ($post = $posts_stmt->fetch()) {
                                $poster = get_user_row_by_id($post['user_id']);
                                $post_id = $post['id'];
                                $num_hearts = $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id}")->rowCount();
                                $num_comments = $GLOBALS['link']->query("SELECT * FROM `posts_comments` WHERE `post_id` = {$post_id}")->rowCount();

                                echo $handlebars->render("post", [
                                    'postid' => $post['id'],
                                    'userid' => $post['user_id'],
                                    'profile_hash' => $poster['profile_hash'],
                                    'fullname' => $poster['fullname'],
                                    'text' => nl2br($post['text']),
                                    'time' => friendly_time($post['date'], $CUR_USER['language']),
                                    'num_hearts' => $num_hearts,
                                    'num_comments' => $num_comments,
                                    'user_pic' => get_user_pp_by_id($id),
                                    'hearted' => $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount() > 0,
                                    'isPic' => $post['image_id'] == null ? 'noPic' : 'yesPic',
                                    'image' => $post['image_id'] == null ? '' : $URL . '/' . get_image_path_by_id($post['image_id']),
                                    'self' => $post['user_id'] == $_SESSION['user_id'] ? 'self' : '',
                                    'write_a_comment_text' => $translate['write_a_comment'][$CUR_USER['gender']],
                                    'update_action_text' => $translate['update'][$CUR_USER['gender']]
                                ]);
                            }
                        ?>

                        <?php if ($posts_stmt->rowCount() == 0) : ?>
                            <p style="color: #999"><?php echo $translate['profile_has_no_content']; ?></p>
                        <?php endif; ?>
                    </div>

                    <div id="feed-loader"></div>

                    <script id="post-template" type="text/x-handlebars-template">
                        <?php include 'templates/post.hbs'; ?>
                    </script>
                </div>

                <div class="col-md-4 order-md-2 order-1">
                    <?php if ($_SESSION['user_id'] != $id) : ?>
                        <div id="profile-date-invite-link-wrap">
                            <?php if ($GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE `user_one_id` = {$_SESSION['user_id']} AND `user_two_id` = {$id} AND `date` > DATE_SUB(NOW(), INTERVAL 1 MONTH)")->rowCount() == 0) : ?>
                                <a href="#" data-userid="<?php echo $id; ?>" class="date-invitation-trigger" id="profile-date-invite-link"><img src="<?php echo $URL; ?>/img/icons/flower.svg" height="25px"> <?php echo $translate['send_a_date_request'][$CUR_USER['gender']]; ?></a>
                            <?php else : ?>
                                <div id="profile-date-invitation-already-invited">ניתן לשלוח בקשה לדייט אחת לחודש לאותו משתמש.</div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div id="profile-information-wrap">
                        <?php if ($user['profession'] != "" || $user['company'] != "" || $id == $_SESSION['user_id']) : ?> 
                            <div class="profile-information-item editable-wrap" id="profile-job-info-item">
                                <div class="editable-icon"></div>
                                <div class="title"><?php echo $translate['work']; ?></div>
                                <div class="item-content" id="job-item-content">
                                    <?php if ($user['profession'] != "") : ?>
                                        <strong><?php echo $user['profession']; ?></strong>
                                    <?php endif; ?>

                                    <?php if ($user['company'] != "") : ?>
                                        ב<?php echo $user['company']; ?>
                                    <?php endif; ?>
                                </div>

                                <?php if ($id == $_SESSION['user_id']) : ?>
                                    <div class="editable-content">
                                        <div id="profile-profession-select-wrap">
                                            <input type="text" class="profile-info-edit-input" id="profession-input" placeholder="בחר מקצוע" value="<?php echo htmlspecialchars($user['profession']); ?>">
                                            <input type="text" class="profile-info-edit-input" id="company-input" placeholder="בחר חברה" value="<?php echo htmlspecialchars($user['company']); ?>">
                                            <button id="job-update-btn" class="cute-btn"><?php echo $translate['update'][$CUR_USER['gender']]; ?></button>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="profile-information-item editable-wrap" id="profile-education-info-item">
                            <div class="editable-icon"></div>
                            <div class="title"><?php echo $translate['education']; ?></div>
                            <div class="item-content" id="education-item-content"><?php echo $user['education']; ?></div>

                            <?php if ($id == $_SESSION['user_id']) : ?>
                                <div class="editable-content">
                                    <div id="profile-profession-select-wrap">
                                        <input type="text" class="profile-info-edit-input" id="education-input" placeholder="פרט על ההשכלה שלך" value="<?php echo htmlspecialchars($user['education']); ?>">
                                        <button id="education-update-btn" class="cute-btn"><?php echo $translate['update'][$CUR_USER['gender']]; ?></button>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="profile-information-item editable-wrap">
                            <div class="editable-icon"></div>
                            <div class="title"><?php echo $translate['languages']; ?></div>
                            <div class="item-content">
                                <?php if ($CUR_USER['language'] == 'he') : ?>
                                    עברית, ערבית, ספרדית, אנגלית
                                <?php else: ?>
                                    English, Spanish, Hebrew
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="profile-information-item editable-wrap" id="profile-information-item-info">
                            <div class="editable-icon"></div>
                            <div class="title"><?php echo $translate['private_info']; ?></div>
                            <div class="item-content">
                                <div id="personal-info-list">
                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['relationship_status']; ?></div>
                                        <div class="det">רווק</div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['orientation']; ?></div>
                                        <div class="det">
                                            <?php if ($user['orientation'] == 'both') echo 'ביסקסואל'; ?>
                                            <?php if ($user['gender'] == 'male' && $user['orientation'] == 'male') echo 'הומוסקסואל'; ?>
                                            <?php if ($user['gender'] == 'female' && $user['orientation'] == 'female') echo 'לסבית'; ?>
                                            <?php if ($user['gender'] == 'female' && $user['orientation'] == 'male') echo 'סטרייטית'; ?>
                                            <?php if ($user['gender'] == 'male' && $user['orientation'] == 'female') echo 'סטרייט'; ?>
                                        </div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['height']; ?></div>
                                        <div class="det" id="height-det"><?php echo height_format($user['height']); ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['weight']; ?></div>
                                        <div class="det" id="weight-det"><?php echo $user['weight']; ?> ק"ג</div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['body_shape']; ?></div>
                                        <div class="det" id="body-type-det"><?php echo $user['body_type']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['hair_color']; ?></div>
                                        <div class="det" id="hair-color-det"><?php echo $user['hair_color']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['eye_color']; ?></div>
                                        <div class="det" id="eye-color-det"><?php echo $user['eye_color']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['zodiac']; ?></div>
                                        <div class="det" id="zodiac-det"><?php echo $user['zodiac']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['children']; ?></div>
                                        <div class="det" id="children-det"><?php echo $user['children'] == 0 ? 'אין' : $user['children']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['smoking']; ?></div>
                                        <div class="det" id="smoking-det"><?php echo $user['smoking'] ? 'מעשן' : 'לא מעשן'; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title"><?php echo $translate['alcohol']; ?></div>
                                        <div class="det" id="alcohol-det">שותה מעט</div>
                                    </div>
                                </div>
                            </div>

                            <div class="editable-wrap">
                                <input type="text" class="profile-info-edit-input" data-col="fullname" id="fullname-input" placeholder="שם מלא" value="<?php echo $user['fullname']; ?>">
                                <input type="text" class="profile-info-edit-input" data-col="nickname" id="nickname-input" placeholder="כינוי" value="<?php echo $user['nickname']; ?>">
                                <select class="profile-info-edit-input" data-col="relationship" id="relationship-input" value="<?php echo $user['relationship']; ?>">
                                    <option value="<?php echo $user['relationship']; ?>"><?php echo $translate['relationship_status']; ?></option>
                                    <option value="<?php echo genderize_text('רווק'); ?>"><?php echo genderize_text('רווק'); ?></option>
                                    <option value="<?php echo genderize_text('גרוש'); ?>"><?php echo genderize_text('גרוש'); ?></option>
                                    <option value="<?php echo genderize_text('אלמן'); ?>"><?php echo genderize_text('אלמן'); ?></option>
                                </select>
                                
                                <select class="profile-info-edit-input" data-col="height" id="height-input" placeholder="<?php echo $translate['height']; ?>" value="<?php echo $user['height']; ?>">
                                    <option value="<?php echo $user['height']; ?>"><?php echo $translate['choose_height'][$CUR_USER['gender']]; ?></option>
                                    <?php ranged_options(150, 220, false); ?>
                                </select>

                                <select class="profile-info-edit-input" data-col="weight" id="weight-input" placeholder="<?php echo $translate['weight']; ?>" value="<?php echo $user['weight']; ?>">
                                    <option value="<?php echo $user['weight']; ?>"><?php echo $translate['choose_weight'][$CUR_USER['gender']]; ?></option>
                                    <?php ranged_options(45, 150, false); ?>
                                </select>
                                
                                <select class="profile-info-edit-input" data-col="body_type" id="body-type-input">
                                    <option value="<?php echo $user['body_type']; ?>"><?php echo $translate['body_shape']; ?></option>
                                    <option value="אתלטי">אתלטי</option>
                                    <option value="ממוצע">ממוצע</option>
                                    <option value="שרירי">שרירי</option>
                                    <option value="רזה">רזה</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="hair_color" id="hair-color-input">
                                    <option value="<?php echo $user['hair_color']; ?>"><?php echo $translate['hair_color']; ?></option>
                                    <option value="חום בהיר">חום בהיר</option>
                                    <option value="בלונד">בלונד</option>
                                    <option value="חום כהה">חום כהה</option>
                                    <option value="שחור">שחור</option>
                                    <option value="צבוע">צבוע</option>
                                    <option value="קירח">קירח</option>
                                    <option value="לבן">לבן</option>
                                    <option value="מגולח">מגולח</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="eye_color" id="eye-color-input">
                                    <option value="<?php echo $user['eye_color']; ?>"><?php echo $translate['eye_color']; ?></option>
                                    <option value="שחור">שחור</option>
                                    <option value="חום">חום</option>
                                    <option value="כחול">כחול</option>
                                    <option value="ירוק">ירוק</option>
                                    <option value="אחר">אחר</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="zodiac" id="zodiac-input">
                                    <option value="<?php echo $user['zodiac']; ?>"><?php echo $translate['zodiac']; ?></option>
                                    <option value="טלה">טלה</option>
                                    <option value="שור">שור</option>
                                    <option value="תאומים">תאומים</option>
                                    <option value="סרטן">סרטן</option>
                                    <option value="אריה">אריה</option>
                                    <option value="בתולה">בתולה</option>
                                    <option value="מאזניים">מאזניים</option>
                                    <option value="עקרב">עקרב</option>
                                    <option value="קשת">קשת</option>
                                    <option value="גדי">גדי</option>
                                    <option value="דלי">דלי</option>
                                    <option value="דגים">דגים</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="children" id="children-input">
                                    <option value="<?php echo $user['children']; ?>" selected="selected"><?php echo $translate['children']; ?></option>
                                    <?php ranged_options(0, 10); ?>
                                </select>

                                <select class="profile-info-edit-input" data-col="smoking" id="smoking-input">
                                    <option value="<?php echo $user['smoking']; ?>"><?php echo $translate['smoking']; ?></option>
                                    <option value="0">לא מעשן</option>
                                    <option value="1" <?php if ($user['smoking']) { echo 'selected="selected"'; } ?>>מעשן</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="" id="alcohol-input">
                                    <option value="<?php echo $user['alcohol']; ?>"><?php echo $translate['alcohol']; ?></option>
                                    <option value="לא שותה">לא שותה</option>
                                    <option value="שותה מעט">שותה מעט</option>
                                    <option value="שותה הרבה">שותה הרבה</option>
                                </select>

                                <button id="info-update-btn" class="cute-btn"><?php echo $translate['update'][$CUR_USER['gender']]; ?></button>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="profile-information-item editable-wrap">
                            <div class="title"><?php echo $translate['interests']; ?></div>

                            <div class="item-content">
                                <div id="profile-hobbies">
                                    <?php if ($id == $_SESSION['user_id']) : ?>
                                        <div class="hobby-item" id="new-hobby-btn">
                                            <div class="title"><i class="fas fa-plus"></i></div>
                                        </div>
                                    <?php endif; ?>

                                    <?php $hobbies_stmt = $GLOBALS['link']->query("SELECT * FROM `users_hobbies` WHERE `user_id` = {$id} AND `active`"); ?>
                                    <?php while ($hobby = $hobbies_stmt->fetch()) : ?>
                                        <div class="hobby-item" onclick="delete_hobby(<?php echo $hobby['id']; ?>); $(this).remove();">
                                            <div class="title"><?php echo $hobby['text']; ?></div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>

                                <?php if ($id == $_SESSION['user_id']) : ?>
                                    <p id="hobbies-delete-warning"><?php echo $translate['click_on_interest_to_delete']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $URL; ?>/js/feed.js"></script>
<script src="<?php echo $URL; ?>/js/profile.js"></script>

<?php if ($id != $_SESSION['user_id']) : ?>
    <style>
        .editable-icon {
            display: none;
        }
    </style>
<?php endif; ?>