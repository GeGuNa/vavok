<?php 
// (c) vavok.net
require_once"../include/strtup.php";

if (!is_reg() || (!isadmin('', 101) && !isadmin('', 102))) {
    header ("Location: ../?errorAuth");
    exit;
} 

$my_title = $lang_admin['sitestats'];
include_once"../themes/$config_themes/index.php";

echo '<p><img src="../images/img/webstats.png" alt="">  ' . $lang_admin['sitestats'] . '<br /><br /></p>';

echo '<p><a href="../pages/counter.php" class="sitelink"> ' . $lang_admin['visitstats'] . '</a><br />';
echo '<a href="../pages/online.php" class="sitelink"> ' . $lang_admin['usronline'] . '</a></p>';

echo '<p><br /><br />
<a href="./" class="sitelink">' . $lang_home['admpanel'] . '</a><br>';
echo '<a href="../" class="homepage">' . $lang_home['home'] . '</a></p>';

include_once"../themes/$config_themes/foot.php";

?>
