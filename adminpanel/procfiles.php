<?php 
// (c) vavok.net - Aleksandar Vranešević
// modified: 15.04.2020. 23:50:28
// todo: rewrite whole page

require_once"../include/strtup.php";
require_once"../include/htmlbbparser.php";

if (!is_reg() || (!is_administrator(101) && !chkcpecprm('pageedit', 'show'))) {
    header("Location: ../?isset=ap_noaccess");
    exit;
}

// init page editor
$pageEditor = new Page;

if (isset($_GET['action'])) {
    $action = check($_GET['action']);
}

if (isset($_GET['file'])) {
    $file = check($_GET['file']);

    // get page id we work with
    $page_id = $pageEditor->get_page_id("file='" . $file . "'");
} elseif (isset($_POST['file'])) {
    $file = check($_POST['file']);

    // get page id we work with
    $page_id = $pageEditor->get_page_id("file='" . $file . "'");
} else {
    $file = '';
}

if (isset($_POST['text_files'])) {
    $text_files = $_POST['text_files'];
} 

$config_editfiles = 10;
$time = time();


if ($action == "editfile") {
    // get edit mode
    if (!empty($_SESSION['edmode'])) {
        $edmode = check($_SESSION['edmode']);
    } else {
        $edmode = 'columnist';
        $_SESSION['edmode'] = $edmode;
    } 

    if (!empty($file) && !empty($text_files)) {
        $page_info = $pageEditor->select_page($page_id, 'crtdby, published');

        if (!chkcpecprm('pageedit', 'show') && !is_administrator(101)) {
            header("Location: index.php?isset=ap_noaccess");
            exit;
        } 

        if ($page_info['crtdby'] != $user_id && !chkcpecprm('pageedit', 'edit') && (!chkcpecprm('pageedit', 'editunpub') || $page_info['published'] != 1) && !is_administrator(101)) {
            header("Location: index.php?isset=ap_noaccess");
            exit;
        }

        // bug when magic quotes are on and '\' sign
        // if magic quotes are on we don't want ' to become \'
        if (get_magic_quotes_gpc()) {
            // strip all slashes
            $text_files = stripslashes($text_files);
        } 

        $text_files = str_replace('{INBOX}', '<?php echo \'<a href="\' . BASEDIR . \'pages/inbox.php">\' . $lang_home[\'inbox\'] . \'</a>\'; ?>', $text_files);
        $text_files = str_replace('{INBOXMSGS}', '<?php echo \'(\' . $users->user_mail($user_id) . \')\'; ?>', $text_files);
        $text_files = str_replace('{LOGOUT}', '<?php echo \'<a href="\' . BASEDIR . \'pages/input.php?action=exit">Log out</a>\'; ?>', $text_files);

        $text_files = str_replace('{BASEDIR}', '<?php echo BASEDIR; ?>', $text_files);

        // update db data
        $pageEditor->update($page_id, $text_files);
    } 

    header("Location: files.php?action=edit&file=$file&isset=mp_editfiles");
    exit;
}

// update head tags on all pages
if ($action == 'editmainhead') {
    if (!$users->is_administrator(101)) {
        redirect_to("../?isset=ap_noaccess");
    } 

    // update header data
    file_put_contents("../used/headmeta.dat", $text_files);

    redirect_to("files.php?action=mainmeta&isset=mp_editfiles");
}

// update head tags on specific page
if ($action == "editheadtag") {
    // get default image link
    $image = !empty($_POST['image']) ? check($_POST['image']) : '';

    // update header tags
    if (!empty($file)) {

        // who created page
        $page_info = $pageEditor->select_page($page_id, 'crtdby');

        // check can user see page
        if (!chkcpecprm('pageedit', 'show') && !$users->is_administrator(101)) {
            redirect_to("Location: index.php?isset=ap_noaccess");
        }

        // check can user edit page
        if (!chkcpecprm('pageedit', 'edit') && !$users->is_administrator(101) && $page_info['crtdby'] != $user_id) {
            redirect_to("Location: index.php?isset=ap_noaccess");
        } 

        // update db data
        $data = array(
            'headt' => $text_files,
            'default_img' => $image
        );
        $pageEditor->head_data($page_id, $data);

        // redirect
        redirect_to("files.php?action=headtag&file=$file&isset=mp_editfiles");

    } 
    // fields must not be empty
    redirect_to("files.php?action=headtag&file=$file&isset=mp_noeditfiles");
}

// rename page
if ($action == "renamepg") {
    $pg = check($_POST['pg']); // new file name

    if (!empty($pg) && !empty($file)) {
        $page_info = $pageEditor->select_page($page_id, 'crtdby');

        if (!chkcpecprm('pageedit', 'show') && !is_administrator(101)) {
            header("Location: index.php?isset=ap_noaccess");
            exit;
        } 
        if (!chkcpecprm('pageedit', 'edit') && !is_administrator(101) && $page_info['crtdby'] != $user_id) {
            header("Location: index.php?isset=ap_noaccess");
            exit;
        } 

        // rename page
        $pageEditor->rename($pg, $page_id);

        header("Location: files.php?action=edit&file=$pg&isset=mp_editfiles");
        exit;
    } 
    header("Location: files.php?action=edit&file=$pg&isset=mp_noedit");
    exit;
}

if ($action == "addnew") {

if (!chkcpecprm('pageedit', 'insert') && !is_administrator(101)) {

redirect_to("index.php?isset=ap_noaccess");

}

$newfile = isset($_POST['newfile']) ? check($_POST['newfile']) : '';
$type = isset($_POST['type']) ? check($_POST['type']) : '';

$page_title = $newfile;
$newfile = strtolower(trans($newfile));

if (isset($_POST['lang']) && !empty($_POST['lang'])) {

$pagelang = check($_POST['lang']);

$pagelang_file = '!.' . $pagelang . '!';

} else {

$pagelang = '';

}

if (!empty($newfile)) {

$newfiles = $newfile . '' . $pagelang_file . '.php';

// check if page exists
$includePageLang = !empty($pagelang) ? " AND lang='" . $pagelang . "'" : '';

if ($pageEditor->page_exists('', "pname='" . $newfile . "'" . $includePageLang)) {
    header ("Location: files.php?action=new&isset=mp_pageexists");
    exit;
}

// insert db data
$values = array(
'pname' => $newfile,
'lang' => $pagelang,
'created' => time(),
'lastupd' => time(),
'lstupdby' => $user_id,
'file' => $newfiles,
'crtdby' => $user_id,
'published' => '1',
'pubdate' => '0',
'tname' => $page_title,
'headt' => '<meta property="og:title" content="' . $page_title . '" />',
'type' => $type
);

// insert data
$pageEditor->insert($values);

// file successfully created
header ("Location: files.php?action=edit&file=$newfiles&isset=mp_newfiles");
exit;
} else {
header ("Location: files.php?action=new&isset=mp_noyesfiles");
exit;
} 
}

if ($action == "del") {
    if (!chkcpecprm('pageedit', 'del') && !is_administrator(101)) {
        header("Location: index.php?isset=ap_noaccess");
        exit;
    }

    // delete page
    $pageEditor->delete($page_id);
 
    header("Location: files.php?isset=mp_delfiles");
    exit;
}

// publish page; page will be avaliable for visitors
if ($action == "publish") {
    if (!empty($page_id)) {

        if (!chkcpecprm('pageedit', 'publish') && !is_administrator(101)) {
            header("Location: index.php?isset=ap_noaccess");
            exit;
        }

        // update db data
        $pageEditor->visibility($page_id, 2);
    } 

    header("Location: files.php?action=show&file=" . $file . "&isset=mp_editfiles");
    exit;
} 
// unpublish page
if ($action == "unpublish") {
    if (!empty($page_id)) {

        if (!chkcpecprm('pageedit', 'publish') && !is_administrator(101)) {
            header("Location: index.php?isset=ap_noaccess");
            exit;
        } 
        // update db data
        $pageEditor->visibility($page_id, 1);
    } 

    header("Location: files.php?action=show&file=" . $file . "&isset=mp_editfiles");
    exit;
} 
// update page language
if ($action == 'pagelang') {
    if (!is_administrator(101)) {
        header("Location: ../?isset=ap_noaccess");
        exit;
    }


    $pageId = check($_GET['id']);
    $lang = check($_POST['lang']);

    // update database data
    $pageEditor->language($pageId, $lang);

    $pageData = $pageEditor->select_page($pageId);
    header("Location: files.php?action=show&file=" . $pageData['pname'] . "!." . $lang . "!.php&isset=mp_editfiles");
    exit;
} 

?>