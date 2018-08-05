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
        <div id="main-sidebar-profile-card-pp"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
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
            <div class="item">
                <div class="pic"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
                <div class="textual">
                    <div class="fullname">כוסית אש</div>
                    <div class="time">לפני שעה</div>
                </div>
            </div>

            <div class="item">
                <div class="pic"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
                <div class="textual">
                    <div class="fullname">כוסית אש</div>
                    <div class="time">לפני שעה</div>
                </div>
            </div>

            <div class="item">
                <div class="pic"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
                <div class="textual">
                    <div class="fullname">כוסית אש</div>
                    <div class="time">לפני שעה</div>
                </div>
            </div>

            <div class="item">
                <div class="pic"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
                <div class="textual">
                    <div class="fullname">כוסית אש</div>
                    <div class="time">לפני שעה</div>
                </div>
            </div>

            <div class="item">
                <div class="pic"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
                <div class="textual">
                    <div class="fullname">כוסית אש</div>
                    <div class="time">לפני שעה</div>
                </div>
            </div>

            <div class="item">
                <div class="pic"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
                <div class="textual">
                    <div class="fullname">כוסית אש</div>
                    <div class="time">לפני שעה</div>
                </div>
            </div>

            <div class="item">
                <div class="pic"><img src="<?php echo $URL; ?>/img/pp.jpg" alt=""></div>
                <div class="textual">
                    <div class="fullname">כוסית אש</div>
                    <div class="time">לפני שעה</div>
                </div>
            </div>
        </div>
    </div>

    <div id="sidebar-credit">
        <a href="#" class="footer-link">צור קשר</a>
        <a href="#" class="footer-link">אודות</a>
        <a href="#" class="footer-link">תנאי שימוש</a>
        <a href="#" class="footer-link">תכנית שותפים</a>
        <div>
            כל הזכויות שמורות לאלפא דייט 2018 ©
        </div>
    </div>
</div>