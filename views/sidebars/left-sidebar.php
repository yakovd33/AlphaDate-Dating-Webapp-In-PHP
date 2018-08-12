<?php if ($CUR_USER['orientation'] == null || $CUR_USER['city'] == null) : ?>
    <div class="card" id="info-complete-card">
        <h6 id="info-complete-card-title">השלמת פרטים</h6>

        <?php if ($CUR_USER['orientation'] == null) : ?>
            <label class="user-info-edit-label">משיכה מינית</label>
            <select class="profile-info-edit-input" data-col="orientation" id="orientation-input">
                <option value="male">גברים</option>
                <option value="female">נשים</option>
                <option value="borth">הכל</option>
            </select>
        <?php endif; ?>

        <?php if ($CUR_USER['orientation'] == null) : ?>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

            <label class="user-info-edit-label">עיר</label>
            <select class="profile-info-edit-input" data-col="city" id="city-input">
                <?php $cities_stmt = $GLOBALS['link']->query("SELECT * FROM `cities`"); ?>

                <option value="">בחר עיר מגורים</option>
                <?php while ($city = $cities_stmt->fetch()) : ?>
                    <option value="<?php echo $city['name']; ?>"><?php echo $city['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <script>
                $("#city-input").select2({});
            </script>
        <?php endif; ?>

        <button id="initial-info-update-btn" class="cute-btn">עדכן</button>

        <script>
            $("#initial-info-update-btn").click(function () {
                if ($("#city-input").length > 0) {
                    data = new FormData();
                    data.append('col', 'orientation');
                    data.append('value',  $("#orientation-input").val());
                    
                    $.ajax({
                        url: URL + '/update_col/',
                        processData: false,
                        contentType: false,
                        method : 'POST',
                        data : data,
                        success: function (response) {
                            if ($("#city-input").length > 0) {
                                data = new FormData();
                                data.append('col', 'city');
                                data.append('value',  $("#city-input").val());
                                
                                $.ajax({
                                    url: URL + '/update_col/',
                                    processData: false,
                                    contentType: false,
                                    method : 'POST',
                                    data : data,
                                    success: function (response) {
                                        $("#info-complete-card").remove();
                                    }
                                });
                            }
                        }
                    });
                }
            });
        </script>
        <div class="clearfix"></div>
    </div>
<?php endif; ?>

<div class="card" id="main-sidebar-profile-card">
    <div id="main-sidebar-profile-card-visual">
        <div id="main-sidebar-profile-card-pp"><img src="<?php echo get_user_pp_by_id($CUR_USER['id']); ?>" alt=""></div>
        <div id="main-sidebar-profile-card-textuals">
            <div class="fullname"><?php echo $CUR_USER['fullname']; ?></div>
            <div class="nickname"><?php echo $CUR_USER['nickname']; ?></div>
        </div>
    </div>

    <div id="main-sidebar-profile-card-numeral">
        <div class="item">
            <div class="number"><?php echo get_user_popularity($CUR_USER['id']); ?>%</div>
            <div class="text">פופולאריות</div>
        </div>

        <div class="item">
            <div class="number"><?php echo $CUR_USER['flowers']; ?></div>
            <div class="text">פרחים</div>
        </div>

        <div class="item">
            <div class="number"><?php echo $CUR_USER['meetings']; ?></div>
            <div class="text">פגישות</div>
        </div>
    </div>
</div>

<div id="sidebar-story-section">
    <!-- <div id="sidebar-story-title">הסטורי</div> -->

    <!-- <div id="sidebar-story-items" class="card"> -->
    <div id="sidebar-story-items">
        <div id="sidebar-story-items-list">
            <div id="sidebar-story-add-btn">
                <div class="icon"><i class="fas fa-plus"></i></div>
                <div class="text">הוסף לסטורי שלך</div>
            </div>

            <?php $recent_stories_users_stmt = $GLOBALS['link']->query("SELECT DISTINCT `user_id` AS `uid` FROM `stories` WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) AND `user_id` <> {$_SESSION['user_id']} " . get_user_blocked_user_by_col('user_id')); ?>
            
            <?php
                // Sort stories by date
                $users_last_stories = [];

                while ($story_user = $recent_stories_users_stmt->fetch()) {
                    $uid = $story_user['uid'];
                    $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `date` DESC LIMIT 1")->fetch();
                    array_push($users_last_stories, $user_last_story);
                }

                usort($users_last_stories, 'sort_by_date');
                
                foreach ($users_last_stories as $story) {
                    $story['user_id'] . ' ' . $story['date'] . '<br>';
                }

                $self_story_stmt = $GLOBALS['link']->query("SELECT DISTINCT `user_id` AS `uid` FROM `stories` WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) AND `user_id` = {$_SESSION['user_id']}");
                
                // Filter seen stories
                $unseen_stories = [];
                $seen_stories = [];

                foreach ($users_last_stories as $story) {
                    if (has_user_seen_story($story['id'])) {
                        array_push($seen_stories, $story);
                    } else {
                        array_push($unseen_stories, $story);
                    }
                }
            ?>

            <?php while ($story = $self_story_stmt->fetch()) : ?>
                <?php $uid = $_SESSION['user_id']; ?>
                <?php $story_user = get_user_row_by_id($uid); ?>
                <?php $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `id` DESC LIMIT 1")->fetch(); ?>
                
                <div class="item" data-userid="<?php echo $_SESSION['user_id']; ?>">
                    <div class="pic"><img src="<?php echo get_user_pp_by_id($uid); ?>" alt=""></div>
                    <div class="textual">
                        <div class="fullname">הסטורי שלי</div>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php foreach ($unseen_stories as $story) : ?>
                <?php $uid = $story['user_id']; ?>
                <?php $story_user = get_user_row_by_id($uid); ?>
                <?php $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `id` DESC LIMIT 1")->fetch(); ?>

                <div class="item" data-userid="<?php echo $uid; ?>">
                    <div class="pic"><img src="<?php echo get_user_pp_by_id($uid); ?>" alt=""></div>
                    <div class="textual">
                        <div class="fullname"><?php echo $story_user['fullname']; ?></div>
                        <div class="time"><?php echo $user_last_story['date']; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($seen_stories) > 0) : ?>
                <div id="seen-stories-title">סטוריז שראית כבר</div>
            <?php endif; ?>

            <?php foreach ($seen_stories as $story) : ?>
                <?php $uid = $story['user_id']; ?>
                <?php $story_user = get_user_row_by_id($uid); ?>
                <?php $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `id` DESC LIMIT 1")->fetch(); ?>

                <div class="item" data-userid="<?php echo $uid; ?>">
                    <div class="pic"><img src="<?php echo get_user_pp_by_id($uid); ?>" alt=""></div>
                    <div class="textual">
                        <div class="fullname"><?php echo $story_user['fullname']; ?></div>
                        <div class="time"><?php echo friendly_time($user_last_story['date']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="sidebar-credit">
        <a href="<?php echo $URL; ?>/contact/" class="footer-link">צור קשר</a>
        <a href="<?php echo $URL; ?>/about/" class="footer-link">אודות</a>
        <a href="<?php echo $URL; ?>/terms/" class="footer-link">תנאי שימוש</a>
        <a href="<?php echo $URL; ?>/contact/" class="footer-link">תכנית שותפים</a>
        <div>
            כל הזכויות שמורות לאלפא דייט 2018 ©
        </div>
    </div>
</div>

<div id="new-story-adder-wrap">
    <div id="new-story-pic">
        <div id="new-story-pic-text"></div>
    </div>

    <div id="new-story-text-wrap">
        <div id="new-story-text">
            <input type="text" id="new-story-text-input" placeholder="טקסט סטורי">
            <input type="color" id="new-story-text-color-input" value="#ffffff">
        </div>

        <div id="choose-text-type">טקסט עם רקע</div>
        <input type="checkbox" id="new-story-text-type-checkbox">
    </div>

   <button class="cute-btn" id="new-story-choose-pic">הוסף תמונה</button>
   <input type="file" id="new-story-image-input" accept="image/x-png,,image/jpeg" style="display: none">

   <button id="submit-new-story" class="cute-btn">העלה</button>
</div>

<div id="story-showcase-wrap">
    <div id="story-showcase">
        <div id="story-showcase-content">
            <a href="" id="story-user-profile-link">
                <div id="story-showcase-user-dets">
                    <div class="pp"><img src="<?php echo get_user_pp_by_id(1); ?>" alt=""></div>
                    <div class="fullname">יעקב שטרית</div>
                    <div class="time">6 שעות</div>
                </div>
            </a>

            <div id="story-hourglasses">
                <span class="story-hourglass"><span class="story-hourglass-spent"></span></span>
                <span class="story-hourglass"><span class="story-hourglass-spent"></span></span>
                <span class="story-hourglass"><span class="story-hourglass-spent"></span></span>
            </div>

            <div id="story-pic">
                <div id="story-pic-text"></div>
            </div>
        </div>
    </div>
</div>