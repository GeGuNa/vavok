<?php
/**
 * Author:    Aleksandar Vranešević
 * URI:       https://vavok.net
 */

require_once '../include/startup.php';

// Page settings
$vavok->go('current_page')->page_title = $vavok->go('localization')->string('lostpass');

$vavok->go('current_page')->append_head_tags('<link rel="stylesheet" href="../themes/templates/pages/registration/lost_password.css" />');

// Add data to page <head> to show Google reCAPTCHA
$vavok->go('current_page')->append_head_tags('<script src="https://www.google.com/recaptcha/api.js" async defer></script>');

$vavok->require_header();

if (empty($vavok->post_and_get('page')) || $vavok->post_and_get('page') == 'index') {
	$this_page = new PageGen('pages/registration/lost_password.tpl');

    // Show reCAPTCHA
    $this_page->set('security_code', '<div class="g-recaptcha" data-sitekey="' . $vavok->get_configuration('recaptcha_sitekey') . '"></div>');

	echo $this_page->output();
}

// Send mail
if ($vavok->post_and_get('page') == 'send') {
    if (!empty($vavok->post_and_get('logus')) && !empty($vavok->post_and_get('mailsus'))) {
        $userx_id = $vavok->go('users')->getidfromnick($vavok->post_and_get('logus'));
        $show_userx = $vavok->go('db')->get_data('vavok_about', "uid='" . $userx_id . "'", 'email');

        $checkmail = trim($show_userx['email']);

        if ($vavok->post_and_get('mailsus') == $checkmail) {
			if ($vavok->recaptcha_response($vavok->post_and_get('g-recaptcha-response'))['success'] == true) {

                $newpas = $vavok->generate_password();
                $new = $vavok->go('users')->password_encrypt($newpas);

                $subject = $vavok->go('localization')->string('newpassfromsite') . ' ' . $vavok->get_configuration('title');
                $mail = $vavok->go('localization')->string('hello') . " " . $vavok->post_and_get('logus') . "<br /><br />
                " . $vavok->go('localization')->string('yournewdata') . " " . $vavok->get_configuration('homeUrl') . "<br /><br />
                " . $vavok->go('localization')->string('username') . ": " . $vavok->post_and_get('logus') . "<br />
                " . $vavok->go('localization')->string('pass') . ": " . $newpas . "<br /><br />
                " . $vavok->go('localization')->string('lnkforautolog') . ":<br />
                " . $vavok->get_configuration('homeUrl') . "/pages/input.php?log=" . $vavok->post_and_get('logus') . "&pass=" . $newpas . "&cookietrue=1<br /><br />
                " . $vavok->go('localization')->string('ycchngpass');

				$send_mail = new Mailer();
				$send_mail->queue_email($vavok->post_and_get('mailsus'), $subject, $mail);

                // Update user's profile
                $vavok->go('db')->update('vavok_users', 'pass', $new, "id='{$userx_id}'");

                echo '<p><b>' . $vavok->go('localization')->string('passgen') . '</b></p>';
            } else {
                echo '<p>' . $vavok->go('localization')->string('wrongcaptcha') . '!</p>';

                echo '<p><a href="lostpassword.php" class="btn btn-outline-primary sitelink">' . $vavok->go('localization')->string('back') . '</a></p>';
            } 
        } else {
            echo '<p>' . $vavok->go('localization')->string('wrongmail') . '!</p>';

            echo '<p><a href="lostpassword.php" class="btn btn-outline-primary sitelink">' . $vavok->go('localization')->string('back') . '</a></p>';
        } 
    } else {
        echo '<p>' . $vavok->go('localization')->string('noneededdata') . '!</p>';

        echo '<p><a href="lostpassword.php" class="btn btn-outline-primary sitelink">' . $vavok->go('localization')->string('back') . '</a></p>';
    } 
} 

echo $vavok->homelink('<p>', '</p>');

$vavok->require_footer();

?>