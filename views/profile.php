<?php
    if (!is_logged()) {
        header("Location: " . $URL);
    }

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $id = $CUR_USER['id'];
    }

    if (is_user_blocked($id)) {
        header("Location: " . $URL);
    }

    $user = get_user_row_by_id($id);
    $age = $GLOBALS['link']->query("SELECT YEAR(CURDATE()) - YEAR(`date_of_birth`) AS `age` FROM `users` WHERE `id` = {$id}")->fetch()['age'];

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
        } else {
            // Increase streak
            $GLOBALS['link']->query("UPDATE `profile_views` SET `streak` = `streak` + 1 WHERE `viewer_id` = {$_SESSION['user_id']} AND `viewed_id` = {$id} AND `date` = CURDATE()");
        }
    }
?>

<link rel="stylesheet" href="<?php echo $URL; ?>/css/profile.css">

<div class="container" id="site-wrap">
    <div class="row">
        <div class="col-md-3">
            <div class="profile-card">
                <div class="pp self">
                    <img src="<?php echo get_user_pp_by_id($id); ?>">
                    <?php if ($user['gender'] != null) : ?>
                        <div id="gender-marker" class="<?php echo $user['gender']; ?>" style="display: none"></div>
                    <?php endif; ?>

                    <input type="file" id="self-pp-changer-input" accept="image/x-png,,image/jpeg">
                </div>

                <div class="fullname"><?php echo $user['fullname']; ?>, <?php echo $age; ?></div>
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

                    <button id="about-me-update-btn" class="cute-btn">עדכן</button>
                    <div class="clearfix"></div>
                </div>

                <div class="profile-information-item editable-wrap">
                    <div class="item-content">
                        <div id="profile-pics">
                            <?php $user_pics_stmt = $GLOBALS['link']->query("SELECT * FROM `images` WHERE `user_id` = {$id} AND NOT `is_pp` AND NOT `is_message` AND NOT `is_story` ORDER BY `date` DESC LIMIT 6"); ?>

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
                        echo $handlebars->render("new_post", [
                            'fullname' => $CUR_USER['fullname'],
                            'nickname' => $CUR_USER['nickname'],
                            'user_pic' => get_user_pp_by_id($_SESSION['user_id'])
                        ]);
                    ?>

                    <div id="feed-posts">
                        <?php
                            $posts_query = "SELECT * FROM `posts` WHERE `user_id` = {$id} ORDER BY `date` DESC LIMIT " . get_setting('posts_per_page');
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
                                    'fullname' => $poster['fullname'],
                                    'text' => nl2br($post['text']),
                                    'time' => friendly_time($post['date']),
                                    'num_hearts' => $num_hearts,
                                    'num_comments' => $num_comments,
                                    'hearted' => $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount() > 0
                                ]);
                            }
                        ?>
                    </div>

                    <script id="post-template" type="text/x-handlebars-template">
                        <?php include 'templates/post.hbs'; ?>
                    </script>
                </div>

                <div class="col-md-4 order-md-2 order-1">
                    <?php if ($_SESSION['user_id'] != $id) : ?>
                        <div id="profile-date-invite-link-wrap">
                            <?php if ($GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE `user_one_id` = {$_SESSION['user_id']} AND `user_two_id` = {$id} AND `date` > DATE_SUB(NOW(), INTERVAL 1 MONTH)")->rowCount() == 0) : ?>
                                <a href="#" data-userid="<?php echo $id; ?>" class="date-invitation-trigger" id="profile-date-invite-link"><img src="<?php echo $URL; ?>/img/icons/flower.svg" height="25px"> שלח הצעה לדייט</a>
                            <?php else : ?>
                                <div id="profile-date-invitation-already-invited">ניתן לשלוח בקשה לדייט אחת לחודש לאותו משתמש.</div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div id="profile-information-wrap">
                        <?php if ($user['profession'] != "" || $user['company'] != "" || $id == $_SESSION['user_id']) : ?> 
                            <div class="profile-information-item editable-wrap" id="profile-job-info-item">
                                <div class="editable-icon"></div>
                                <div class="title">עבודה</div>
                                <div class="item-content">
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
                                            <button id="job-update-btn" class="cute-btn">עדכן</button>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="profile-information-item editable-wrap" id="profile-education-info-item">
                            <div class="editable-icon"></div>
                            <div class="title">השכלה</div>
                            <div class="item-content"><?php echo $user['education']; ?></div>

                            <?php if ($id == $_SESSION['user_id']) : ?>
                                <div class="editable-content">
                                    <div id="profile-profession-select-wrap">
                                        <input type="text" class="profile-info-edit-input" id="education-input" placeholder="פרט על ההשכלה שלך" value="<?php echo htmlspecialchars($user['education']); ?>">
                                        <button id="education-update-btn" class="cute-btn">עדכן</button>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="profile-information-item editable-wrap">
                            <div class="editable-icon"></div>
                            <div class="title">שפות</div>
                            <div class="item-content">
                                עברית, ערבית, ספרדית, אנגלית
                            </div>
                        </div>

                        <div class="profile-information-item editable-wrap" id="profile-information-item-info">
                            <div class="editable-icon"></div>
                            <div class="title">פרטים אישיים</div>
                            <div class="item-content">
                                <div id="personal-info-list">
                                    <div class="personal-info-item">
                                        <div class="title">מערכת יחסים</div>
                                        <div class="det">רווק</div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">מיניות</div>
                                        <div class="det">
                                            <?php if ($user['orientation'] == 'both') echo 'ביסקסואל'; ?>
                                            <?php if ($user['gender'] == 'male' && $user['orientation'] == 'male') echo 'הומוסקסואל'; ?>
                                            <?php if ($user['gender'] == 'female' && $user['orientation'] == 'female') echo 'לסבית'; ?>
                                            <?php if ($user['gender'] == 'female' && $user['orientation'] == 'male') echo 'סטרייטית'; ?>
                                            <?php if ($user['gender'] == 'male' && $user['orientation'] == 'female') echo 'סטרייט'; ?>
                                        </div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">גובה</div>
                                        <div class="det"><?php echo height_format($user['height']); ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">משקל</div>
                                        <div class="det"><?php echo $user['weight']; ?> ק"ג</div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">מבנה גוף</div>
                                        <div class="det"><?php echo $user['body_type']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">צבע שיער</div>
                                        <div class="det"><?php echo $user['hair_color']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">צבע עיניים</div>
                                        <div class="det"><?php echo $user['eye_color']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">מזל</div>
                                        <div class="det"><?php echo $user['zodiac']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">ילדים</div>
                                        <div class="det"><?php echo $user['children'] == 0 ? 'אין' : $user['children']; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">עישון</div>
                                        <div class="det"><?php echo $user['smoking'] ? 'מעשן' : 'לא מעשן'; ?></div>
                                    </div>

                                    <div class="personal-info-item">
                                        <div class="title">אלכוהול</div>
                                        <div class="det">שותה מעט</div>
                                    </div>
                                </div>
                            </div>

                            <div class="editable-wrap">
                                <input type="text" class="profile-info-edit-input" data-col="fullname" id="fullname-input" placeholder="שם מלא" value="<?php echo $user['fullname']; ?>">
                                <input type="text" class="profile-info-edit-input" data-col="nickname" id="nickname-input" placeholder="כינוי" value="<?php echo $user['nickname']; ?>">
                                <select class="profile-info-edit-input" data-col="relationship" id="relationship-input" value="<?php echo $user['relationship']; ?>">
                                    <option value="<?php echo $user['relationship']; ?>">מערכת יחסים</option>
                                    <option value="<?php echo genderize_text('רווק'); ?>"><?php echo genderize_text('רווק'); ?></option>
                                    <option value="<?php echo genderize_text('גרוש'); ?>"><?php echo genderize_text('גרוש'); ?></option>
                                    <option value="<?php echo genderize_text('אלמן'); ?>"><?php echo genderize_text('אלמן'); ?></option>
                                </select>
                                
                                <select class="profile-info-edit-input" data-col="height" id="height-input" placeholder="גובה" value="<?php echo $user['height']; ?>">
                                    <option value="<?php echo $user['height']; ?>">בחר גובה</option>
                                    <?php ranged_options(150, 220, false); ?>
                                </select>

                                <select class="profile-info-edit-input" data-col="weight" id="weight-input" placeholder="משקל" value="<?php echo $user['weight']; ?>">
                                    <option value="<?php echo $user['weight']; ?>">בחר משקל</option>
                                    <?php ranged_options(45, 150, false); ?>
                                </select>
                                
                                <select class="profile-info-edit-input" data-col="body_type" id="body-type-input">
                                    <option value="<?php echo $user['body_type']; ?>">מבנה גוף</option>
                                    <option value="אתלטי">אתלטי</option>
                                    <option value="ממוצע">ממוצע</option>
                                    <option value="שרירי">שרירי</option>
                                    <option value="רזה">רזה</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="hair_color" id="hair-color-input">
                                    <option value="<?php echo $user['hair_color']; ?>">צבע שיער</option>
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
                                    <option value="<?php echo $user['eye_color']; ?>">צבע עיניים</option>
                                    <option value="שחור">שחור</option>
                                    <option value="חום">חום</option>
                                    <option value="כחול">כחול</option>
                                    <option value="ירוק">ירוק</option>
                                    <option value="אחר">אחר</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="zodiac" id="zodiac-input">
                                    <option value="<?php echo $user['zodiac']; ?>">מזל</option>
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
                                    <option value="<?php echo $user['children']; ?>" selected="selected">ילדים</option>
                                    <?php ranged_options(0, 10); ?>
                                </select>

                                <select class="profile-info-edit-input" data-col="smoking" id="smoking-input">
                                    <option value="<?php echo $user['smoking']; ?>">עישון</option>
                                    <option value="0">לא מעשן</option>
                                    <option value="1" <?php if ($user['smoking']) { echo 'selected="selected"'; } ?>>מעשן</option>
                                </select>

                                <select class="profile-info-edit-input" data-col="" id="alcohol-input">
                                    <option value="<?php echo $user['alcohol']; ?>">אלכוהול</option>
                                    <option value="לא שותה">לא שותה</option>
                                    <option value="שותה מעט">שותה מעט</option>
                                    <option value="שותה הרבה">שותה הרבה</option>
                                </select>

                                <button id="info-update-btn" class="cute-btn">עדכן</button>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                        <div class="profile-information-item editable-wrap">
                            <div class="title">תחומי עניין</div>

                            <div class="editable-icon"></div>

                            <div class="item-content">
                                <div id="profile-hobbies">
                                    <div class="hobby-item">
                                        <div class="icon"><i class="fas fa-cocktail"></i></div>
                                        <div class="title">מין זמין בטעם חמין</div>
                                    </div>

                                    <div class="hobby-item">
                                        <div class="icon"><i class="fas fa-futbol"></i></div>
                                        <div class="title">טניס</div>
                                    </div>

                                    <div class="hobby-item">
                                        <div class="icon"><i class="fas fa-futbol"></i></div>
                                        <div class="title">טניס</div>
                                    </div>

                                    <div class="hobby-item">
                                        <div class="icon"><i class="fas fa-futbol"></i></div>
                                        <div class="title">טניס</div>
                                    </div>

                                    <div class="hobby-item">
                                        <div class="icon"><i class="fas fa-futbol"></i></div>
                                        <div class="title">טניס</div>
                                    </div>

                                    <div class="hobby-item">
                                        <div class="icon"><i class="fas fa-futbol"></i></div>
                                        <div class="title">טניס</div>
                                    </div>
                                </div>
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