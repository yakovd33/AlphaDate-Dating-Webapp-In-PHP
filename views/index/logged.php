<?php include 'views/header.php'; ?>

<link rel="stylesheet" href="<?php echo $URL; ?>/css/index.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/css/index-logged.css">

<div class="row">
    <div class="col-md-3" id="right-sidebar-wrap">
        <?php include 'views/sidebars/right-sidebar.php'; ?>
    </div>

    <div class="index-main-col <?php if (isset($_GET['tab'])) { echo $_GET['tab']; } else { echo 'hon'; } ?> <?php if (isset($_GET['tab'])) { echo $_GET['tab']; } ?> <?php if (!isset($_GET['tab']) || ($_GET['tab'] != 'profiles') && ($_GET['tab'] != 'meetings')) { echo 'col-md-5'; } else { echo 'col-md-9'; } ?>">
        <div id="index-tabs">
            <?php if (!isset($_GET['tab'])) : ?>
                <?php include 'tabs/hot-or-not.php'; ?>
            <?php else: ?>
                <?php if ($_GET['tab'] == 'feed') : ?>
                    <?php include 'tabs/feed.php'; ?>
                <?php endif; ?>

                <?php if ($_GET['tab'] == 'profiles') : ?>
                    <?php include 'tabs/profiles.php'; ?>
                <?php endif; ?>

                <?php if ($_GET['tab'] == 'flowers') : ?>
                    <?php include 'tabs/flowers.php'; ?>
                <?php endif; ?>

                <?php if ($_GET['tab'] == 'matches') : ?>
                    <?php include 'tabs/matches.php'; ?>
                <?php endif; ?>

                <?php if ($_GET['tab'] == 'meetings') : ?>
                    <?php include 'tabs/meetings.php'; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!isset($_GET['tab']) || ($_GET['tab'] != 'profiles' && $_GET['tab'] != 'meetings')) : ?>
        <div class="col-md-4">
            <?php if (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] != 'profiles' && $_GET['tab'] != 'meetings')) : ?>
                <?php include 'views/sidebars/left-sidebar.php'; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'views/footer.php'; ?>
<script src="<?php echo $URL; ?>/js/index.js"></script>
<script src="<?php echo $URL; ?>/js/index-logged.js"></script>