<!-- <img src="img/icon.png" height="50px" alt=""> -->

<div id="main-right-sidebar-profile-card-visual">
    <div id="main-right-sidebar-profile-card-pp">
        <a href="<?php echo $URL; ?>/profile/"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></a>
    </div>

    <div id="main-right-sidebar-profile-card-textuals">
        <a href="<?php echo $URL; ?>/profile/">
            <div class="fullname"><?php echo $CUR_USER['fullname']; ?></div>
            <div class="nickname"><?php echo $CUR_USER['nickname']; ?></div>
        </a>
    </div>
</div>

<div id="sidebar-flowers-section">
    <img src="<?php echo $URL; ?>/img/icons/flower.svg" height="30px" alt="">
    <div id="sidebar-flower-counter"><span class="num"><?php echo $CUR_USER['flowers']; ?></span> פרחים</div>
    <a href="#"><button id="buy-flowers-btn"><img src="<?php echo $URL; ?>/img/icons/flower-white.svg" alt=""> קנה פרחים</button></a>
</div>

<?php
    if (isset($_GET['tab'])) {
        $tab = $_GET['tab'];
    } else if (!isset($_GET['page'])) {
        $tab = 'hot-or-not';
    } else {
        $tab = '';
    }
?>

<div id="right-sidebar-links">
    <a href="<?php echo $URL; ?>" class="link <?php if ($tab == 'hot-or-not') { echo 'active'; } ?>">היכרויות <span class="nav-link-filter-icon" id="hot-or-not-nav-link-options"></span></a>
    <a href="<?php echo $URL; ?>/feed/" class="link <?php if ($tab == 'feed') { echo 'active'; } ?>">הפיד</a>
    <a href="<?php echo $URL; ?>/profiles/" class="link <?php if ($tab == 'profiles') { echo 'active'; } ?>">פרופילים <span class="nav-link-filter-icon" id="profiles-nav-link-options"></span></a>
    <a href="<?php echo $URL; ?>/flowers/" class="link <?php if ($tab == 'flowers') { echo 'active'; } ?>">פרחים</a>
    <a href="<?php echo $URL; ?>/matches/" class="link <?php echo $URL; ?>/matches/ <?php if ($tab == 'matches') { echo 'active'; } ?>">התאמות <?php if (get_user_unseen_matches_num() > 0) : ?> <span class="rigt-sidebar-item-num"><?php echo get_user_unseen_matches_num(); ?></span> <?php endif; ?></a>
    <a href="<?php echo $URL; ?>/meetings/" class="link <?php echo $URL; ?>/meetings/ <?php if ($tab == 'meetings') { echo 'active'; } ?>">פגישות <?php if (get_user_unseen_meetings_requests_num() > 0) : ?> <span class="rigt-sidebar-item-num"><?php echo get_user_unseen_meetings_requests_num(); ?></span> <?php endif; ?></a>
</div>


<div id="right-sidebar-paid-heads">
    <?php $paid_heads = $GLOBALS['link']->query("SELECT * FROM `paid_heads` WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY `date` DESC LIMIT 10"); ?>
    
    <div class="head" id="add-head">
        
    </div>

    <?php while ($head = $paid_heads->fetch()) : ?>
        <a href="<?php echo $URL; ?>/profile/<?php echo $head['user_id']; ?>">
            <div class="head">
                <img src="<?php echo $URL; ?>/<?php echo get_user_pp_by_id($head['user_id']); ?>">
            </div>
        </a>
    <?php endwhile; ?>
</div>