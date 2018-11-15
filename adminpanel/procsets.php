<?php 
// (c) vavok.net
require_once"../include/strtup.php";

if (!is_reg() || !isadmin('', 101)) {
    header("Location: ../?error");
    exit;
}

if (isset($_GET['action'])) {$action = check($_GET['action']);}


// main settings update
if ($action == "editone") {
    if (isset($_POST['conf_set0']) && isset($_POST['conf_set1']) && $_POST['conf_set2'] != "" && $_POST['conf_set3'] != "" && $_POST['conf_set8'] != "" && $_POST['conf_set9'] != "" && $_POST['conf_set10'] != "" && $_POST['conf_set11'] != ""  && !empty($_POST['conf_set12']) && $_POST['conf_set14'] != "" && !empty($_POST['conf_set21']) && $_POST['conf_set29'] != "" && isset($_POST['conf_set61']) && isset($_POST['conf_set62']) && isset($_POST['conf_set63'])) {
        $ufile = file(BASEDIR . "used/config.dat");
        $udata = explode("|", $ufile[0]);

    	$udata[0] = check($_POST['conf_set0']);
        $udata[1] = check($_POST['conf_set1']);
        $udata[2] = check($_POST['conf_set2']);
        $udata[3] = check($_POST['conf_set3']);
        $udata[8] = check($_POST['conf_set8']);
        $udata[9] = htmlspecialchars(stripslashes(trim($_POST['conf_set9'])));
        $udata[10] = check($_POST['conf_set10']);
        $udata[11] = check($_POST['conf_set11']);
        $udata[12] = check($_POST['conf_set12']);
        $udata[14] = check($_POST['conf_set14']);
        $udata[21] = check($_POST['conf_set21']); // transfer protocol
        $udata[29] = (int)$_POST['conf_set29'];
        $udata[47] = check($_POST['conf_set47']);
        $udata[61] = (int)$_POST['conf_set61'];
        $udata[62] = (int)$_POST['conf_set62'];
        $udata[63] = (int)$_POST['conf_set63'];


        for ($u = 0; $u < $config["configKeys"]; $u++) {
            $utext .= $udata[$u] . '|';
        } 

        if (!empty($udata[8]) && !empty($udata[9])) {
            $fp = fopen(BASEDIR . "used/config.dat", "a+");
            flock($fp, LOCK_EX);
            ftruncate($fp, 0);
            fputs($fp, $utext);
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            unset($utext);
        } 
        header ("Location: setting.php?isset=mp_yesset");
        exit;
    } else {
        header ("Location: setting.php?action=setone&isset=mp_nosset");
        exit;
    } 
} 


if ($action == "edittwo") {
if ($_POST['conf_set4'] != "" && $_POST['conf_set5'] != "" && $_POST['conf_set7'] != "" && $_POST['conf_set74'] != "") {
$ufile = file(BASEDIR . "used/config.dat");
$udata = explode("|", $ufile[0]);

$udata[4] = (int)$_POST['conf_set4'];
$udata[5] = (int)$_POST['conf_set5'];
$udata[7] = (int)$_POST['conf_set7'];
$udata[74] = (int)$_POST['conf_set74'];

for ($u = 0; $u < $config["configKeys"]; $u++) {
    $utext .= $udata[$u] . '|';
} 

if (!empty($udata[8]) && !empty($udata[9])) {
    $fp = fopen(BASEDIR . "used/config.dat", "a+");
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fputs($fp, $utext);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    unset($utext);
} 
header ("Location: setting.php?isset=mp_yesset");
exit;
} else {
header ("Location: setting.php?action=settwo&isset=mp_nosset");
exit;
} 
} 

if ($action == "editthree") {
if ($_POST['conf_set17'] != "" && $_POST['conf_set18'] != "" && $_POST['conf_set19'] != "" && $_POST['conf_set20'] != "" && $_POST['conf_set22'] != "" && $_POST['conf_set23'] != "" && $_POST['conf_set24'] != "" && $_POST['conf_set25'] != "" && $_POST['conf_set56'] != "") {
$ufile = file(BASEDIR . "used/config.dat");
$udata = explode("|", $ufile[0]);

$udata[17] = (int)$_POST['conf_set17'];
$udata[18] = (int)$_POST['conf_set18'];
$udata[19] = (int)$_POST['conf_set19'];
$udata[20] = (int)$_POST['conf_set20'];
$udata[22] = (int)$_POST['conf_set22'];
$udata[23] = (int)$_POST['conf_set23'];
$udata[24] = (int)$_POST['conf_set24'];
$udata[25] = (int)$_POST['conf_set25'];
$udata[56] = (int)$_POST['conf_set56'];
$udata[63] = (int)$_POST['conf_set63'];
$udata[64] = (int)$_POST['conf_set64'];
$udata[65] = (int)$_POST['conf_set65'];

for ($u = 0; $u < $config["configKeys"]; $u++) {
    $utext .= $udata[$u] . '|';
} 

if (!empty($udata[8]) && !empty($udata[9])) {
    $fp = fopen(BASEDIR . "used/config.dat", "a+");
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fputs($fp, $utext);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    unset($utext);
} 
header ("Location: setting.php?isset=mp_yesset");
exit;
} else {
header ("Location: setting.php?action=setthree&isset=mp_nosset");
exit;
} 
} 

if ($action == "editfour") {
if ($_POST['conf_set26'] != "" && $_POST['conf_set27'] != "" && $_POST['conf_set38'] != "" && $_POST['conf_set39'] != "" && $_POST['conf_set49'] != "") {
// update main config
$ufile = file(BASEDIR . "used/config.dat");
$udata = explode("|", $ufile[0]);

$udata[26] = (int)$_POST['conf_set26'];
$udata[27] = (int)$_POST['conf_set27'];
if (!empty($_POST['conf_set28'])) {
$udata[28] = (int)$_POST['conf_set28'];
}
$udata[37] = (int)$_POST['conf_set37'];
$udata[38] = (int)$_POST['conf_set38'];
$udata[38] = $udata[38] * 1024;
$udata[38] = (int)$udata[38];
$udata[39] = (int)$_POST['conf_set39'];
$udata[49] = (int)$_POST['conf_set49'];
$udata[68] = (int)$_POST['conf_set68'];

for ($u = 0; $u < $config["configKeys"]; $u++) {
    $utext .= $udata[$u] . '|';
} 

    $fp = fopen(BASEDIR . "used/config.dat", "a+");
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fputs($fp, $utext);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    unset($utext);


// update gallery settings
$gallery_file = file(BASEDIR . "used/dataconfig/gallery.dat");
if ($gallery_file) {
    $gallery_data = explode("|", $gallery_file[0]);

    $gallery_data[0] = (int)$_POST['gallery_set0'];
    $gallery_data[8] = (int)$_POST['gallery_set8']; // photos per page
    $gallery_data[5] = (int)$_POST['screen_width'];
    $gallery_data[6] = (int)$_POST['screen_height'];
    $gallery_data[7] = (int)$_POST['media_buttons'];


    for ($u = 0; $u < $config["configKeys"]; $u++) {
        $gallery_text .= $gallery_data[$u] . '|';
    } 

    if (isset($gallery_data[0])) {
        $fp = fopen(BASEDIR . "used/dataconfig/gallery.dat", "a+");
        flock($fp, LOCK_EX);
        ftruncate($fp, 0);
        fputs($fp, $gallery_text);
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        unset($gallery_text);
    }
}

header ("Location: setting.php?isset=mp_yesset");
exit;
} else {
header ("Location: setting.php?action=setfour&isset=mp_nosset");
exit;
} 
} 

if ($action == "editfive") {
if ($_POST['conf_set30'] != "") {
$ufile = file(BASEDIR . "used/config.dat");
$udata = explode("|", $ufile[0]);

$udata[30] = (int)$_POST['conf_set30'];

for ($u = 0; $u < $config["configKeys"]; $u++) {
    $utext .= $udata[$u] . '|';
} 

if (!empty($udata[8]) && !empty($udata[9])) {
    $fp = fopen(BASEDIR . "used/config.dat", "a+");
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fputs($fp, $utext);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    unset($utext);
} 
header ("Location: setting.php?isset=mp_yesset");
exit;
} else {
header ("Location: setting.php?action=setfive&isset=mp_nosset");
exit;
} 
}

if ($action == "editseven") {
if (empty($_POST['conf_set6']) || empty($_POST['conf_set31']) || empty($_POST['conf_set51']) || empty($_POST['conf_set70'])) {
$ufile = file(BASEDIR . "used/config.dat");
$udata = explode("|", $ufile[0]);

$udata[6] = (int)$_POST['conf_set6'];
$udata[31] = (int)$_POST['conf_set31'];
$udata[51] = (int)$_POST['conf_set51'];
$udata[70] = (int)$_POST['conf_set70'];

for ($u = 0; $u < $config["configKeys"]; $u++) {
    $utext .= $udata[$u] . '|';
} 

if (!empty($udata[8]) && !empty($udata[9])) {
    $fp = fopen(BASEDIR . "used/config.dat", "a+");
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fputs($fp, $utext);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    unset($utext);
} 
header ("Location: setting.php?isset=mp_yesset");
exit;
} else {
header ("Location: setting.php?action=setseven&isset=mp_nosset");
exit;
} 
} 

if ($action == "editeight") {
if ($_POST['conf_set58'] != "" && $_POST['conf_set76'] != "") {
$ufile = file(BASEDIR . "used/config.dat");
$udata = explode("|", $ufile[0]);

$udata[58] = (int)$_POST['conf_set58'];
$udata[76] = round($_POST['conf_set76'] * 1440);

for ($u = 0; $u < $config["configKeys"]; $u++) {
    $utext .= $udata[$u] . '|';
} 

if (!empty($udata[8]) && !empty($udata[9])) {
    $fp = fopen(BASEDIR . "used/config.dat", "a+");
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fputs($fp, $utext);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    unset($utext);
} 
header ("Location: setting.php?isset=mp_yesset");
exit;
} else {
header ("Location: setting.php?action=seteight&isset=mp_nosset");
exit;
} 
} 
// edit database settings
if ($action == "editnine") {
if ($_POST['conf_set77'] != "" && $_POST['conf_set78'] != "" && $_POST['conf_set79'] != "" && $_POST['conf_set80'] != "") {
$ufile = file(BASEDIR . "used/config.dat");
$udata = explode("|", $ufile[0]);

$udata[77] = $_POST['conf_set77'];
$udata[78] = $_POST['conf_set78'];
$udata[79] = $_POST['conf_set79'];
$udata[80] = $_POST['conf_set80'];

for ($u = 0; $u < $config["configKeys"]; $u++) {
    $utext .= $udata[$u] . '|';
} 

if (!empty($udata[8]) && !empty($udata[9])) {
    $fp = fopen(BASEDIR . "used/config.dat", "a+");
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    fputs($fp, $utext);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    unset($utext);
} 
header ("Location: setting.php?isset=mp_yesset&amp;" . SID);
exit;
} else {
header ("Location: setting.php?action=setnine&isset=mp_nosset&amp;" . SID);
exit;
} 
}


?>
