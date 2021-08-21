<div id="new-story-adder-wrap">
    <div id="new-story-pic">
        <div id="new-story-pic-disclamer">*תמונות עלולות להיראות מתוחות במצב תצוגה מקדימה</div>
        <div id="new-story-pic-text"></div>
    </div>

    <div id="new-story-text-wrap">
        <div id="new-story-text">
            <input type="text" id="new-story-text-input" placeholder="טקסט סטורי">
            <input type="color" id="new-story-text-color-input" value="#ffffff">
        </div>

        <div id="new-story-text-colors-wrap">
            <div id="new-story-text-colors">
                <div class="color" style="background-color: #000;"></div>
                <div class="color" style="background-color: #fff; border: 1px solid #000;"></div>
                <div class="color" id="new-story-rand-color" style="background-color: #ebebeb;"><i class="fas fa-random"></i></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
                <div class="color" style="background-color: #<?php echo random_color(); ?>"></div>
            </div>
        </div>

        <div id="choose-text-type">טקסט עם רקע</div>
        <div class="clearfix"></div>
        <input type="checkbox" id="new-story-text-type-checkbox">
    </div>

   <button class="cute-btn" id="new-story-choose-pic">בחר תמונה</button>
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

                <div id="story-num-views"><i class="fas fa-eye"></i> <div id="story-num-views-num"></div></div>
            </div>
        </div>
    </div>
</div>