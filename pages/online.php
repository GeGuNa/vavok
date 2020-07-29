<?php 
/*
* (c) Aleksandar Vranešević
* Author:    Aleksandar Vranešević
* URI:       https://vavok.net
* Updated:   26.07.2020. 17:36:26
*/

require_once"../include/startup.php";

if (get_configuration('showOnline') == 0 && (!$users->is_reg() && !$users->is_administrator())) redirect_to("../");

// page settings
$data_on_page = 10; // online users per page

$my_title = 'Online';
require_once BASEDIR . "themes/" . MY_THEME . "/index.php";

echo '<p><img src="../images/img/online.gif" alt=""> <b>' . $localization->string('whoisonline') . '</b></p>';

$total = $db->count_row(DB_PREFIX . 'online');
$totalreg = $db->count_row(DB_PREFIX . 'online', "user > 0");

if (!empty($_GET['list'])) {
    $list = check($_GET['list']);
} else {
    if ($totalreg > 0) {
        $list = 'reg';
    } else {
        $list = 'full';
    } 
} 
if ($list != 'full' && $list != 'reg') {
    $list = 'full';
}

$page = isset($_GET['page']) ? check($_GET['page']) : 1;

if (isset($_GET['start'])) {
    $start = check($_GET['start']);
} 

echo $localization->string('totonsite') . ': <b>' . (int)$total . '</b><br />' . $localization->string('registered') . ':  <b>' . (int)$totalreg . '</b><br /><hr>';

if ($list == "full") {

    $navigation = new Navigation($data_on_page, $total, $page, 'online.php?'); // start navigation

    $start = $navigation->start()['start']; // starting point 

    $full_query = "SELECT * FROM " . DB_PREFIX . "online ORDER BY date DESC LIMIT $start, " . $data_on_page;

    foreach ($db->query($full_query) as $item) {
        $time = date_fixed($item['date'], 'H:i');

        if (($item['user'] == "0" || empty($item['user'])) && empty($item['bot'])) {
            echo '<b>' . $localization->string('guest') . '</b> (' . $localization->string('time') . ': ' . $time . ')<br />';
            if ($users->is_moderator() || $users->is_administrator()) {
                echo '<small><font color="#CC00CC">(<a href="../' . get_configuration('mPanel') . '/ip-informations.php?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
            } 
            echo '<hr />';
        } elseif (!empty($item['bot']) && ($item['user'] == "0" || empty($item['user']))) {
            echo '<b>' . $item['bot'] . '</b> (' . $localization->string('time') . ': ' . $time . ')<br />';
            if ($users->is_moderator() || $users->is_administrator()) {
                echo '<small><font color="#CC00CC">(<a href="../' . get_configuration('mPanel') . '/ip-informations.php?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
            } 
            echo '<hr />';
        } else {
            echo '<b><a href="../pages/user.php?uz=' . $item['user'] . '">' . $users->getnickfromid($item['user']) . '</a></b> (' . $localization->string('time') . ': ' . $time . ')<br />';
            if ($users->is_moderator() || $users->is_administrator()) {
                echo '<small><font color="#CC00CC">(<a href="../' . get_configuration('mPanel') . '/ip-informations.php?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
            } 
            echo '<hr />';
        } 
    } 
} else {
    $total = $totalreg;

    if ($total < 1) {
        echo '<br /><img src="../images/img/reload.gif" alt=""> <b>' . $localization->string('noregd') . '!</b><br />';
    } 

    $navigation = new Navigation($data_on_page, $total, $page, 'online.php?'); // start navigation

    $start = $navigation->start()['start']; // starting point  

    $full_query = "SELECT * FROM " . DB_PREFIX . "online WHERE user > 0 ORDER BY date DESC LIMIT $start, " . $data_on_page;

    foreach ($db->query($full_query) as $item) {
        $time = date_fixed($item['date'], 'H:i');

        echo '<b><a href="../pages/user.php?uz=' . $item['user'] . '">' . $users->getnickfromid($item['user']) . '</a></b> (' . $localization->string('time') . ': ' . $time . ')<br />';
        if ($users->is_moderator() || $users->is_administrator()) {
            echo '<small><font color="#CC00CC">(<a href="../' . get_configuration('mPanel') . '/ip-informations.php?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
        } 
        echo '<hr />';
    } 
} 

echo $navigation->get_navigation();

if ($list != "full") {
    echo'<p><a href="online.php?list=full" class="btn btn-outline-primary sitelink">' . $localization->string('showguest') . '</a></p>';
} else {
    echo'<p><a href="online.php?list=reg" class="btn btn-outline-primary sitelink">' . $localization->string('hideguest') . '</a></p>';
} 

echo '<p><a href="../" class="btn btn-primary homepage">' . $localization->string('home') . '</a></p>';

require_once BASEDIR . "themes/" . MY_THEME . "/foot.php";

?>