<?php
/*
* (c) Aleksandar Vranešević
* Author:    Aleksandar Vranešević
* URI:       http://vavok.net
* Updated:   31.07.2020. 23:32:44
*/

/**
 * Set session data from cookie
 */
if (empty($_SESSION['log']) && !empty($_COOKIE['cookpass']) && !empty($_COOKIE['cooklog']) && !empty($vavok->get_configuration('keypass'))) {

    // decode username from cookie
    $unlog = xoft_decode($_COOKIE['cooklog'], $vavok->get_configuration('keypass'));

    // decode password from cookie
    $unpar = xoft_decode($_COOKIE['cookpass'], $vavok->get_configuration('keypass'));
    
    // search for username provided in cookie
	$cookie_id = $users->getidfromnick($unlog);

    // get user's data
	$cookie_check = $db->get_data('vavok_users', "id='" . $cookie_id . "'", 'name, pass, perm, lang');

    // if user exists
    if (!empty($cookie_check['name'])) {

        // check is password correct
        if ($users->password_check($unpar, $cookie_check['pass']) && $unlog == $cookie_check['name']) {

            $pr_ip = explode(".", $users->find_ip());
            $my_ip = $pr_ip[0] . $pr_ip[1] . $pr_ip[2];


            // write current session data
            $_SESSION['log'] = $unlog;
            $_SESSION['permissions'] = $cookie_check['perm'];
            $_SESSION['my_ip'] = $my_ip;
            $_SESSION['my_brow'] = $users->user_browser();
            $_SESSION['lang'] = $cookie_check['lang'];
            
            // update ip address and last visit time
            $db->update('vavok_users', 'ipadd', $users->find_ip(), "id = '{$cookie_id}'");
            $db->update('vavok_profil', 'lastvst', time(), "uid = '{$cookie_id}'");
        }
    } 
}

?>