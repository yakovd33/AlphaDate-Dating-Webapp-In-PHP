<?php if (is_premium()) : ?>
    <?php
        $profiles_query = "SELECT * FROM `users` WHERE 1";

        if (!isset($_GET['all'])) {
            if ($CUR_USER['orientation'] != 'both') { 
                $profiles_query .= " AND `gender` = '" . $CUR_USER['orientation'] . "'";
            }

            $profiles_query .= " AND (YEAR(`date_of_birth`) < YEAR(CURDATE()) - " . $CUR_USER['interest_age_min'] . ")";
            $profiles_query .= " AND (YEAR(`date_of_birth`) > YEAR(CURDATE()) - " . $CUR_USER['interest_age_max'] . ")";
        }

        $profiles_query .= " AND `id` <> " . $_SESSION['user_id'];
        $profiles_query .= get_user_blocked_user_by_col('id');
        $profiles_query .= " AND NOT `banned`";

        $order_by = " ORDER BY `popularity`";
        if (isset($_POST['sorting']) && $_POST['sorting'] != 'popularity') {
            $sorting = $_POST['sorting'];

            if ($sorting == 'age') {
                $order_by = " ORDER BY `date_of_birth`";
            } else if ($sorting == 'age-desc') {
                $order_by = " ORDER BY `date_of_birth` DESC";
            }
        }

        $profiles_query .= $order_by;

        if (isset($_GET['pagination'])) {
            $page = $_GET['pagination'];
        } else {
            $page = 0;
        }

        if ($page == 0) {
            $start = 0;
        } else {
            $start = $page - 1;
        }
        
        $no_limit_query = $profiles_query;
        $profiles_query .= " LIMIT " . $start * get_setting('profiles_per_page') . ', ' . get_setting('profiles_per_page');

        $profiles_stmt = $GLOBALS['link']->query($profiles_query);
    ?>

    <div id="profiles-tab">
        <form id="profiles-sorting-wrap" method="POST">
            <select class="pretty-select" name="sorting" id="profiles-sort-by" onchange="$(this).parent().submit();">
                <option value="popularity"><?php echo $translate['sort_by']; ?></option>
                <option value="popularity" <?php if (isset($_POST['sorting']) && $_POST['sorting'] == 'popularity') { echo 'selected="selected"'; } ?>><?php echo $translate['popularity']; ?></option>
                <option value="age" <?php if (isset($_POST['sorting']) && $_POST['sorting'] == 'age') { echo 'selected="selected"'; } ?>><?php echo $translate['age_low']; ?></option>
                <option value="age-desc" <?php if (isset($_POST['sorting']) && $_POST['sorting'] == 'age-desc') { echo 'selected="selected"'; } ?>><?php echo $translate['age_high']; ?></option>
            </select>

            <div class="mr-auto" id="profiles-choose-type">
                <a href="<?php echo $URL; ?>/profiles/" class="<?php if (!isset($_GET['all'])) { echo 'active'; } ?>"><?php echo $translate['my_prefrences']; ?></a>
                | 
                <a href="<?php echo $URL; ?>/profiles/all/" class="<?php if (isset($_GET['all'])) { echo 'active'; } ?>"><?php echo $translate['all_profiles']; ?></a>
            </div>
        </form>

        <div class="row" id="profiles-row">
            <?php while ($profile = $profiles_stmt->fetch()) : ?>
                <?php $user = get_user_row_by_id($profile['id']); ?>

                <div class="col-md-4 profiles-tab-profile-wrap">
                    <div class="profiles-tab-profile-card">
                        <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/<?php echo $profile['profile_hash']; ?>/">
                            <div class="pp" style="background-image: url(<?php echo get_user_pp_by_id($profile['id']); ?>);">
                                    <img src="<?php echo get_user_pp_by_id($profile['id']); ?>" style="visibility: hidden">
                                <div class="send-message-btn chatbox-trigger" data-userid="<?php echo $profile['id']; ?>"><i class="fas fa-comment-alt"></i></div>
                            </div>
                        </a>

                        <div class="textual">
                            <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/">
                                <div class="fullname"><?php echo $profile['fullname']; ?> <sub>(<?php echo $user['age']; ?>)</sub></div>
                            </a>

                            <a href="<?php echo $URL; ?>/city/<?php echo $profile['city']; ?>/">
                                <div class="location"><?php echo $profile['city']; ?></div>
                            </a>

                            <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/">
                                <strong class="about-me-label"><?php echo $translate['about_me']; ?>:</strong>
                                <p class="about-me">
                                    <?php echo strlen($profile['about_me']) > 100 ? mb_substr($profile['about_me'], 0, 100)."..." : $profile['about_me']; ?>
                                </p>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div id="pagination">
            <?php
                $profiles_max_page = ceil($GLOBALS['link']->query($no_limit_query)->rowCount() / get_setting('profiles_per_page'));
            ?>

            <?php for ($i = $page - 5; $i <= $page + 5 && $i <= $profiles_max_page; $i++) : ?>
                <?php if ($i > 0) : ?>
                    <a class="page <?php if (($i == $page) || ($page == 0 && $i == 1)) { echo 'active'; } ?>" href="<?php echo $URL; ?>/profiles/<?php echo $i != 1 ? $i : ''; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
<?php else : ?>
    <?php echo js_redirect($URL . '/premium/'); ?>
<?php endif; ?>