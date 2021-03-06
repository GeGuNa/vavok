<?php
/**
 * Author: Aleksandar Vranešević
 * Site:   https://vavok.net
 */

class Page extends BaseModel {
    private string $page_localization;

    /**
     * Homepage data
     * 
     * @param array $params
     * @return array
     */
    public function homepage($params = [])
    {
        // Localization from URL
        $this->page_localization = !empty($params[0]) ? $this->user->getPreferredLanguage($params[0], 'short') : '';

        // Select page with localization or to leave it without localization
        $localization = !empty($params[0]) ? " AND lang = '{$params[0]}'" : '';

        // Get localized page
        $data = $this->db->getData('pages', "pname='index'{$localization}");

        // Page without localization
        if (empty($data)) $data = $this->db->getData('pages', "pname='index'");

        // Update user's language when language is set in URL and it is different then current localization
        if (!empty($this->page_localization) && strtolower($this->page_localization) != $this->user->getPreferredLanguage($_SESSION['lang'], 'short')) {
            $this->user->change_language(strtolower($this->page_localization));
            // Update user's localization for page that we are now loading
            $this->user_data['language'] = $this->user->getPreferredLanguage($this->page_localization);
        }

        // Redirect if user's language is not website default language,
        // language is not in URL, example: www.example.com
        // and page with users's language exists, example: www.example.com/de
        if ($this->configuration('siteDefaultLang') != $this->user->getUserLanguage() && empty($params[0])) $this->redirection(HOMEDIR . $this->user->getPreferredLanguage($this->user->getUserLanguage(), 'short') . '/');

        // Users data
        $data['user'] = $this->user_data;

        return $data = isset($data['tname']) ? $data : die('error404');
    }

    /**
     * Dynamic loading pages from database
     * 
     * @param array $params
     */
    public function dynamic($params = [])
    {
        // Page data
        $this_page = $this->db->getData('pages', "pname='{$params[0]}'");

        // Localization from page's data
        $this->page_localization = !empty($this_page['lang']) ? $this->user->getPreferredLanguage($this_page['lang'], 'short') : '';

        // Update user's language when language is set in URL and it is different then current localization
        if (!empty($this->page_localization) && strtolower($this->page_localization) != $this->user->getPreferredLanguage($_SESSION['lang'], 'short')) {
            $this->user->change_language(strtolower($this->page_localization));
            // Update user's localization for page that we are now loading
            $this->user_data['language'] = $this->user->getPreferredLanguage($this->page_localization);
        }

        // Users data
        $this_page['user'] = $this->user_data;

        // Error 404
        if (!isset($this_page['content'])) $this_page['content'] = 'Error 404';

        return $this_page;
    }

    /**
     * Login page
     * 
     * @param array $data
     * @return array $data
     */
    public function login($data = [])
    {
        // Check login data while logging in
        $data = $this->user->checkAuth();

        // Users data
        $data['user'] = $this->user_data;

        $data['tname'] = '{@localization[login]}}';
        $data['headt'] = '<meta name="robots" content="noindex">';
        $data['headt'] .= '<link rel="stylesheet" href="' . HOMEDIR . 'themes/templates/users/login/login.css">';

        return $data;
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->user->logout();

        header('Location: ' . HOMEDIR);
        exit;
    }

    /**
     * List of users
     */
    public function userlist()
    {
        // Users data
        $data['user'] = $this->user_data;
        $data['tname'] = '{@localization[userlist]}}';
        $data['content'] = '';

        $num_items = $this->user->regmemcount(); // no. reg. members
        $items_per_page = 10;
 
        // Start navigation
        $navigation = new Navigation($items_per_page, $num_items, $this->postAndGet('page'), HOMEDIR . 'pages/userlist/?');

        // Starting point
        $limit_start = $navigation->start()['start'];
        
        if ($num_items > 0) {
            foreach ($this->db->query("SELECT id, name FROM vavok_users ORDER BY name LIMIT $limit_start, $items_per_page") as $item) {
                $data['content'] .= '<div class="a">';
                $data['content'] .= '<a href="' . HOMEDIR . 'users/u/' . $item['id'] . '">' . $item['name'] . '</a> - joined: ' . $this->correctDate($this->user->user_info('regdate', $item['id']), 'd.m.Y.'); // update lang
                $data['content'] .= '</div>';
            }
        }

        $data['content'] .= $navigation->get_navigation();
        $data['content'] .= $this->homelink();

        return $data;
    }

    /**
     * Statistics
     */
    public function statistics()
    {
        // Users data
        $data['user'] = $this->user_data;
        $data['tname'] = '{@localization[statistics]}}';
        $data['content'] = '';

        if ($this->configuration('showCounter') == 6 && !$this->user->administrator()) $this->redirection(HOMEDIR);

        $hour = (int)date("H", time());
        $hday = date("j", time())-1;

        $pcounter_guest = $this->db->count_row('online', "user='0'");

        $pcounter_online = $this->db->count_row('online');

        $pcounter_reg = $pcounter_online - $pcounter_guest;

        $counts = $this->db->getData('counter');

        $clicks_today = $counts['clicks_today'];
        $total_clicks = $counts['clicks_total'];
        $visits_today = $counts['visits_today']; // visits today
        $total_visits = $counts['visits_total']; // total visits
    
        $data['content'] .= '{@localization[temponline]}}: ';
        if ($this->configuration('showOnline') == 1 || $this->user->administrator()) {
            $data['content'] .= '<a href="{@HOMEDIR}}pages/online">' . (int)$pcounter_online . '</a><br />';
        } else {
            $data['content'] .= '<b>' . (int)$pcounter_online . '</b><br />';
        }

        $data['content'] .= '{@localization[registered]}}: <b>' . (int)$pcounter_reg . '</b><br />';
        $data['content'] .= '{@localization[guests]}}: <b>' . (int)$pcounter_guest . '</b><br /><br />';
    
        $data['content'] .= '{@localization[vststoday]}}: <b>' . (int)$visits_today . '</b><br />';
        $data['content'] .= '{@localization[vstpagestoday]}}: <b>' . (int)$clicks_today . '</b><br />';
        $data['content'] .= '{@localization[totvisits]}}: <b>' . (int)$total_visits . '</b><br />';
        $data['content'] .= '{@localization[totopenpages]}}: <b>' . (int)$total_clicks . '</b><br /><br />';
        
        $data['content'] .= $this->homelink('<p>', '</p>');

        return $data;
    }

    /**
     * Users online
     */
    public function online()
    {
        // Users data
        $data['user'] = $this->user_data;
        $data['tname'] = 'Online';
        $data['content'] = '';

        if ($this->configuration('showOnline') == 0 && (!$this->user->userAuthenticated() && !$this->user->administrator())) $this->redirection("../");

        // page settings
        $data_on_page = 10; // online users per page
        
        $data['content'] .= '<p><img src="{@HOMEDIR}}themes/images/img/online.gif" alt=""> <b>' . $this->localization->string('whoisonline') . '</b></p>';
        
        $total = $this->db->count_row('online');
        $totalreg = $this->db->count_row('online', "user > 0");
        
        if (!empty($this->postAndGet('list'))) {
            $list = $this->check($this->postAndGet('list'));
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

        $data['content'] .= $this->localization->string('totonsite') . ': <b>' . (int)$total . '</b><br />' . $this->localization->string('registered') . ':  <b>' . (int)$totalreg . '</b><br /><hr>';
        
        if ($list == 'full') {
            $navigation = new Navigation($data_on_page, $total, HOMEDIR . 'pages/online/?list=full&'); // start navigation
        
            $start = $navigation->start()['start']; // starting point 
        
            $full_query = "SELECT * FROM online ORDER BY date DESC LIMIT $start, " . $data_on_page;
        
            foreach ($this->db->query($full_query) as $item) {
                $time = $this->correctDate($item['date'], 'H:i');
        
                if (($item['user'] == "0" || empty($item['user'])) && empty($item['bot'])) {
                    $data['content'] .= '<b>' . $this->localization->string('guest') . '</b> (' . $this->localization->string('time') . ': ' . $time . ')<br />';
                    if ($this->user->moderator() || $this->user->administrator()) {
                        $data['content'] .= '<small><font color="#CC00CC">(<a href="' . HOMEDIR . $this->configuration('mPanel') . '/ip_informations/?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
                    } 
                    $data['content'] .= '<hr />';
                } elseif (!empty($item['bot']) && ($item['user'] == "0" || empty($item['user']))) {
                    $data['content'] .= '<b>' . $item['bot'] . '</b> (' . $this->localization->string('time') . ': ' . $time . ')<br />';
                    if ($this->user->moderator() || $this->user->administrator()) {
                        $data['content'] .= '<small><font color="#CC00CC">(<a href="' . HOMEDIR . $this->configuration('mPanel') . '/ip_informations/?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
                    } 
                    $data['content'] .= '<hr />';
                } else {
                    $data['content'] .= '<b><a href="' . HOMEDIR . 'users/u/' . $item['user'] . '">' . $this->user->getnickfromid($item['user']) . '</a></b> (' . $this->localization->string('time') . ': ' . $time . ')<br />';
                    if ($this->user->moderator() || $this->user->administrator()) {
                        $data['content'] .= '<small><font color="#CC00CC">(<a href="' . HOMEDIR . $this->configuration('mPanel') . '/ip_informations/?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
                    }
                    $data['content'] .= '<hr />';
                }
            }
        } else {
            $total = $totalreg;
        
            if ($total < 1) {
                $data['content'] .= '<p><img src="{@HOMEDIR}}themes/images/img/reload.gif" alt=""> <b>' . $this->localization->string('noregd') . '!</b></p>';
            }

            $navigation = new Navigation($data_on_page, $total, HOMEDIR . 'online/?'); // start navigation

            $start = $navigation->start()['start']; // starting point
        
            $full_query = "SELECT * FROM online WHERE user > 0 ORDER BY date DESC LIMIT $start, " . $data_on_page;
        
            foreach ($this->db->query($full_query) as $item) {
                $time = $this->correctDate($item['date'], 'H:i');
        
                $data['content'] .= '<b><a href="' . HOMEDIR . 'users/u/' . $item['user'] . '">' . $this->user->getnickfromid($item['user']) . '</a></b> (' . $this->localization->string('time') . ': ' . $time . ')<br />';

                if ($this->user->moderator() || $this->user->administrator()) {
                    $data['content'] .= '<small><font color="#CC00CC">(<a href="' . HOMEDIR . $this->configuration('mPanel') . '/ip_informations/?ip=' . $item['ip'] . '" target="_blank">' . $item['ip'] . '</a>)</font></small>';
                }

                $data['content'] .= '<hr />';
            }
        }

        $data['content'] .= $navigation->get_navigation();

        if ($list != 'full') {
            $data['content'] .= $this->sitelink(HOMEDIR . 'pages/online/?list=full', $this->localization->string('showguest'), '<p>', '</p>');
        } else {
            $data['content'] .= $this->sitelink(HOMEDIR . 'pages/online/?list=reg', $this->localization->string('hideguest'), '<p>', '</p>');
        }

        $data['content'] .= $this->homelink('<p>', '</p>');

        return $data;
    }

    /**
     * Cookies policy
     */
    public function cookies_policy()
    {
        // Users data
        $this_page['user'] = $this->user_data;
        $this_page['tname'] = 'Cookies Policy';
        $this_page['homeurl'] = $this->configuration('homeUrl');

        return $this_page;
    }
}