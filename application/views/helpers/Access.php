<?php

class Zend_View_Helper_Access
{

	public function Access() {
        return $this;
	}

    public function checkAuth() {
        return Zend_Auth::getInstance()->hasIdentity();
    }

    public function getAuth() {
        return Zend_Auth::getInstance()->hasIdentity();
    }

    public function passAuth($message = false) {
        if ($this->checkAuth()) return true;
        else {
            echo '<span class="auth_blocked_content">';
            echo '<a href="autenticar">acesso restrito para usuários</a>';
            echo '</span>';
        }
    }

    public function user($set_object = false, $refresh = false) {
        $userdata = new Zend_Session_Namespace('UserData');
        if (isset($userdata->user)) return $userdata->user;
    }

    public function company($set_object = false, $refresh = false) {
        $userdata = new Zend_Session_Namespace('UserData');
        if (isset($userdata->user->company)) {
            if ($set_object) return new Ee_Model_Data_Company($userdata->user->company);
            return $userdata->user->company;
        }
    }
    
    public function autoLoginLink($user, $url) {
        $hash = sha1($user->login.'eeAutoLogin'.date('YW'));
        $hash .= sha1($user->password.'eeAutoLogin'.date('YW'));
        $url = str_replace('/', '__', $url);
        return 'http://www.empreendemia.com.br/voltar/'.urlencode($url).'/'.$user->id.'/'.$hash;
    }
}