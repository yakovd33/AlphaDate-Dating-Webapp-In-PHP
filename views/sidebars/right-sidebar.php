<!-- <img src="img/icon.png" height="50px" alt=""> -->

<div id="main-right-sidebar-profile-card-visual">
    <div id="main-right-sidebar-profile-card-pp">
        <a href="<?php echo $URL; ?>/profile/"><img src="<?php echo get_user_pp_by_id($CUR_USER['id']); ?>" alt=""></a>
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

<div id="change-prefrences-bar">
    <div id="change-prefrences-bar-arrow"></div>

    <label for="" class="interest-label">מתעניין ב</label>
    <div id="orientation-selection">
        <div class="orientation-item <?php if ($CUR_USER['orientation'] == 'male') { echo 'active'; } ?>" data-value="male">גברים</div>
        <div class="orientation-item <?php if ($CUR_USER['orientation'] == 'female') { echo 'active'; } ?>" data-value="female">נשים</div>
        <div class="orientation-item <?php if ($CUR_USER['orientation'] == 'both') { echo 'active'; } ?>" data-value="both">הכל</div>
    </div>

    <label for="" class="interest-label" id="age-interest-label">בגילאים</label>

    <div id="age-interest-select-wrap">
        <span id="max-age"><?php echo $CUR_USER['interest_age_max']; ?></span>

        <div class="selector">
            <div class="price-slider">
                <div id="slider-range" class="ui-slider ui-corner-all ui-slider-horizontal ui-widget ui-widget-content">
                    <div class="ui-slider-range ui-corner-all ui-widget-header"></div>
                    <span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span><span tabindex="0" class="ui-slider-handle ui-corner-all ui-state-default"></span>
                </div>
            </div>
        </div>

        <span id="min-age"><?php echo $CUR_USER['interest_age_min']; ?></span>
    </div>

    <button id="update-prefrences-btn" class="cute-btn">עדכן</button>
    <div class="clearfix"></div>
</div>

<script>
    $("#slider-range").slider({
        range: true, 
        min: 17,
        max: 120,
        step: 1,
        slide: function( event, ui ) {
            $( "#min-age").html(ui.values[ 0 ]);
            
            suffix = '';
            if (ui.values[ 1 ] == $( "#max-price").data('max') ){
                suffix = ' +';
            }
            $( "#max-age").html(ui.values[ 1 ] + suffix);         
        }
    });

    $("#slider-range").slider('values', 0, <?php echo $CUR_USER['interest_age_min']; ?>); // sets first handle (index 0) to 50
    $("#slider-range").slider('values', 1, <?php echo $CUR_USER['interest_age_max']; ?>); // sets second handle (index 1) to 80
</script>

<div id="right-sidebar-links">
    <a href="<?php echo $URL; ?>" class="link <?php if ($tab == 'hot-or-not') { echo 'active'; } ?>">היכרויות <span class="nav-link-filter-icon" id="hot-or-not-nav-link-options"></span></a>
    <a href="<?php echo $URL; ?>/feed/" class="link <?php if ($tab == 'feed') { echo 'active'; } ?>">הפיד</a>
    <a href="<?php echo $URL; ?>/profiles/" class="link <?php if ($tab == 'profiles') { echo 'active'; } ?>">פרופילים</a>
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