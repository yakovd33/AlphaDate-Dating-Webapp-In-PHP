<?php
    require_once('includes/config.php');
    require_once('includes/functions.php');

    if (!is_logged() && isset($_COOKIE['login_hash']) && $_COOKIE['login_hash'] != 0) {
        $hash = $_COOKIE['login_hash'];
        $_SESSION['user_id'] = $GLOBALS['link']->query("SELECT `user_id` FROM `login_hashes` WHERE `hash` = '{$hash}'")->fetch()['user_id'];
    }

    if (is_logged()) {
        $CUR_USER = get_user_row_by_id($_SESSION['user_id']);
        update_last_seen();
    }

    require_once('includes/language.php');
?>

<?php include 'views/header.php'; ?>

<link rel="stylesheet" href="<?php echo $URL; ?>/css/terms.css">

<?php if ($language == 'he'): ?>
    <h1 id="terms-title">תקנון ותנאי השימוש - אלפא דייט</h1>
    <p id="temrs-disclamer">
        בעת שימוש בשירותי האתר אני מאשר/ת כי קראתי את התקנון ותנאי השימוש ואני מסכים/ה לו ולכל סעיפיו. <br>
        תקנון זה כתוב בלשון זכר אך תקף לכל המינים.
    </p>

    <ol class="terms-list">
        <li>אני מאשר את היותי בן 18 ומעלה.</li>
    </ol>

    <h3 class="terms-list-title">תכנים אסורים</h3>
    <ol class="terms-list">
        <li>תכנים המביעים אלימות כלפי אדם אחר.</li>
        <li>תכנים המכילים תוכן פורנוגרפי, בוטה או פדופילי.</li>
        <li>תכנים המכילים עידוד פגיעה עצמית, גזענות, סקסיזם או כל פעילות פלילית אחרת</li>
        <li>תכנים שנועדו לקידום אינטרסים אישיים</li>
        <li>תוכן זבל או ספאם</li>
        <li>תוכנות פוגעניות, וירוסים, תוכנות נוזקה שונות.</li>
        <li>אין לפרסם תוכן על אדם אחר מבלי לבקש את רשותו.</li>
        <li>תכנים טורדניים או מעליבים.</li>
        <li>טכנים המזכירים אתרים מתחרים.</li>
    </ol>

    <h3 class="terms-list-title">זכויות יוצרים</h3>
    <p>
        זכויות היוצרים של מבנה האתר, עיצובו וקניינו הרוחני שייכות לאתר אלפא דייט.
        
    </p>

    <h3 class="terms-list-title">פרופילים ופרטיות</h3>
    <ol class="terms-list">
        <li>על המידע שמשתמשים מזינים בפרופיל שלהם, כגון, שם, עיר מגורים, גיל ועוד להיות אמיתי.</li>
        <li>הנהלת האתר מתחייבת לשמירת פרטי המשתמשים.</li>
        <li>להנהלת האתר קיימת הזכות להשתמש בפרטי המשתמשים בצורה דמוגרפית.</li>
        <li>הנהלת האתר רשאית גם לעיין בהודעות המשתמשים במקרים כמו דיווח או למטרה תיעודית.</li>
        <li>השימוש באתר הוא באחריות המשתמש בלבד. אין הנהלת האתר אחראית בכל מקרה של פגיעה כלשהי. עם זאת, הנהלת האתר תעשה הכל על מנת למנוע מקרים לא מתבקשים.</li>
        <li>כל עוד לא ביקשתי אחרת, הנהלת האתר רשאית לשלוח לי מיילים שיווקיים או כל תוכן אחר במייל.</li>
        <li>אין הנהלת האתר מתחייבת במענה לתמיכה טכנית או כל פנייה אחרת אך תעשה הכל על מנת לענות לרוב הפניות. <a href="<?php echo $URL; ?>/contact/">צור קשר</a></li>
    </ol>

    <p>
        עם הפרת חוזה זה, רשאית הנהלת האתר להפר את התחייבויותיה כלפי המשתמש המפר,
        הפרת חוזה זה עלולה להוביל לחסימה ותביעה משפטית, בהתאם לחומרת המעשה.
    </p>
<?php else : ?>
    <h1 id="terms-title">Terms and Conditions - Alpha Date</h1>

    <p id="temrs-disclamer">
        By using the website's services, I confirm that I have read the terms and conditions and agree to them and all their sections. <br>
        This policy is written in male language but applies to all genders.
    </p>

    <ol class="terms-list">
        <li>I confirm that I am 18 or older.</li>
    </ol>

    <h3 class="terms-list-title">Prohibited Content</h3>
    <ol class="terms-list">
        <li>Content that incites violence against another person.</li>
        <li>Content that contains pornographic, bot or pedophilic content.</li>
        <li>Content that contains self-harm encouragement, racism, sexism or any other illegal activity</li>
        <li>Content intended to promote personal interests</li>
        <li>Spam or Junk content</li>
        <li>Harmful programs, viruses, various malware.</li>
        <li>Do not post content about another person without their permission.</li>
        <li>Troubling or offending content.</li>
        <li>Techniques that refer to competing sites.</li>
    </ol>

    <h3 class="terms-list-title">Copyright</h3>
    <p>
        The copyright of the website structure, design and intellectual property belongs to the Alpha Date website.
        
    </p>

    <h3 class="terms-list-title">Profiles and Privacy</h3>
    <ol class="terms-list">
        <li>The information that users enter into their profile, such as name, city of residence, age, etc. must be true.</li>
        <li>The website management is committed to maintaining user privacy.</li>
        <li>The website management has the right to use user information for the purpose of improving the website and its services.</li>
        <li>The website management has the right to remove any user profile if it violates the terms and conditions or if it is inactive for a prolonged period of time.</li>
    </ol>

    <p>
        With the breach of this contract, the website management is entitled to breach their obligations towards the breaching user. This breach may result in a ban and legal claim, according to the circumstances
    </p>
<?php endif; ?>

<?php include 'views/footer.php'; ?>