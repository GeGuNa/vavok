 <?php
// (c) vavok.net

require_once"../include/strtup.php";

if ($config["siteOff"] != 1) {
    header("Location: ../");
    exit;
} 

$mediaLikeButton = 'off'; // dont show like buttons

$my_title = "Maintenance";
include_once"../themes/$config_themes/index.php";

echo $lang_page['maintenance'] . '!<br /><br />';

echo '<p><a href="../" class="homepage">' . $lang_home['home'] . '</a></p>';


include_once"../themes/$config_themes/foot.php";
?>