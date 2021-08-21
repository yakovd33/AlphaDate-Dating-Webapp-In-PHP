<?php
    $packages_stmt = $GLOBALS['link']->query("SELECT * FROM `premium_packages` WHERE `is_active`");
?>

<title>אלפא דייט - קניית חשבון פרימיום</title>

<?php include 'nav.php'; ?>

<link rel="stylesheet" href="<?php echo $URL; ?>/css/premium.css">

<div class="background">
  <div class="container">
    <div class="panel pricing-table">
      
        <?php while ($package = $packages_stmt->fetch()) : ?>
            <div class="pricing-plan">
                <img src="<?php echo $URL; ?>/<?php echo $package['icon']; ?>" class="pricing-img">
                <h2 class="pricing-header"><?php echo $package['name']; ?></h2>
                <div class="pricing-description"><?php echo $package['description']; ?></div>
                <ul class="pricing-features">
                    <li class="pricing-features-item"><?php echo $package['flowers']; ?> פרחים</li>
                    <li class="pricing-features-item"><?php echo $package['show_ads'] ? 'יש פרסומות' : 'אין פרסומות'; ?></li>
                    <li class="pricing-features-item">+<?php echo $package['exposure_percentage_increase']; ?>% חשיפה</li>
                    <li class="pricing-features-item"><?php echo $package['are_likes_restricred'] ? 'יש הגבלת לייקים' : 'אין הגבלת לייקים'; ?></li>
                </ul>
                <span class="pricing-price" dir="rtl"><?php echo $package['price']; ?>₪ לחודש</span>
                <a href="#/" class="pricing-button"><?php echo $package['button_text']; ?></a>
            </div>
        <?php endwhile; ?>
    </div>
  </div>
</div>

<script src="<?php echo $URL; ?>/js/premium.js"></script>