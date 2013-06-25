<?php
/**
 * EeMsg.php - EeBot_Controller_Helper_EeMsg
 * 
 * Helper para enviar mensagens e e-mails.
 * 
 * @author Mauro Ribeiro
 * @since 2011-09-06
 */
class EeBot_Controller_Helper_EeMsg extends Zend_Controller_Action_Helper_Abstract
{
    
    /**
     * Envia email usando SMTP do Sendgrid.
     * 
     * @param Zend_Mail $mail               email a ser enviado
     * @param string|boolean $category      categoria para atrelar no Sendgrid
     * @return boolean 
     * @author Mauro Ribeiro
     */
    private function sendEmail($mail, $category = false) {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/ee.ini','production');
        if($config->login->sendgrid->user && $config->login->sendgrid->user != '' && $config->login->sendgrid->password && $config->login->sendgrid->password != '') {
            $login = array(
                'auth' => 'login',
                'username' => $config->login->sendgrid->user,
                'password' => $config->login->sendgrid->password
            );
            $transport = new Zend_Mail_Transport_Smtp('smtp.sendgrid.net', $login);
            if ($category) {
                if (is_array($category)) {
                    $categories_str = implode('","',$category);
                    $mail->addHeader('X-SMTPAPI','{"category":["'.$categories_str.'"]}');   
                }
                else {
                    $mail->addHeader('X-SMTPAPI','{"category":"'.$category.'"}');
                }
            }
            return $mail->send($transport);
        }
        else {
            return $mail->send();
        }
    }

    /**
     * Converte procura um usuário a partir de um id.
     * 
     * @param EeBot_Model_Data_User|int $user
     * @return EeBot_Model_Data_User 
     * @author Mauro Ribeiro
     */
    private function _user($user) {
        if (is_numeric($user)) {
            $user_mapper = new Ee_Model_UserMapper();
            return $user_mapper->find($user);
        }
        else {
             return $user;
        }
    }

    /**
     * Renderiza uma view HTML.
     * 
     * @param string $view_name     nome da view a ser renderizada
     * @param object $vars          parâmetros da view
     * @return string
     */
    private function _render($view_name, $vars) {
        
        // cria objeto da view
        $html = new Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/views/emails/');

        // atribui valores 
        foreach ($vars as $id => $value) {
            $html->assign($id, $value);
        }

        // renderiza
        $bodyText = $html->render($view_name.'.phtml');

        return $bodyText;
    }

    /**
     * Envia e-mail para admins do sistema.
     * 
     * @param string $subject       título do bagulho
     * @param string $body          corpo do email
     * @author Mauro Ribeiro
     */
    public function adminsEmail($subject, $body) {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/ee.ini', 'production');

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($body);
        $mail->setFrom($config->email->noreply->address, $config->email->noreply->name);
        $mail->setReplyTo($config->email->noreply->address, $config->email->noreply->name);
        $mail->addTo($config->email->sysadmins->address, $config->email->sysadmins->name);
        $mail->setSubject('[Empreendemia] '.$subject);
        return $this->sendEmail($mail, 'sysadmins');
    }

    /**
     * Envia email para sysadmins com lista de premiums expirados.
     * 
     * @param array(EeBot_Model_Data_Company) $companies    empresas que expiraram
     * @author Mauro Ribeiro
     */
    public function adminPremiumsExpirations($companies) {
        $view->companies = $companies;
        $render = $this->_render('admins/premium_expirations', $view);
        $subject = 'Cron Diário - Premiums Expirados';

        return $this->adminsEmail($subject, $render);
    }


    /**
     * Envia e-mail para sysadmins com lista de premiums quase expirando.
     * 
     * @param array(EeBot_Model_Data_Company) $companies    empresas que vão expirar
     * @author Mauro Ribeiro
     */
    public function adminExpiringPremiums($companies) {
        $view->companies = $companies;
        $render = $this->_render('admins/expiring_premiums', $view);
        $subject = 'Cron Diário - Premiums expirando daqui 2 dias';

        return $this->adminsEmail($subject, $render);
    }
    
    /**
     * Envia e-mail com nossas recomendações de empresas.
     * 
     * @param $user             usuário que vai receber
     * @author Lucas Gaspar
     * @since 2012-04-16
     */
    public function pendingNotifications($user)
    {

        $view->user = $user;
        $render = $this->_render('pending_notifications', $view);
        $subject = 'Estão esperando sua resposta no Empreendemia';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("millor@empreendemia.com.br", "Millor Machado");
        $mail->setReplyTo("millor@empreendemia.com.br", "Millor Machado");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->setSubject($subject);
        
        // envia o email
        return $this->sendEmail($mail, 'notificacoes pendentes');
    }
    
    /**
     * Envia e-mail com nossas recomendações de empresas.
     * 
     * @param $user             usuário que vai receber
     * @author Lucas Gaspar
     * @since 2012-04-09
     */
    public function someIndicatedCompanies1($user) {
        $view->user = $user;
        $render = $this->_render('some_indicated_companies_1', $view);
        $subject = 'Contatos de empresas do setor industrial no Empreendemia';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("luiz@empreendemia.com.br", "Luiz Piovesana");
        $mail->setReplyTo("luiz@empreendemia.com.br", "Luiz Piovesana");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->setSubject($subject);
        
        // envia o e-mail
        return $this->sendEmail($mail, 'indicacao de algumas empresas');
    }
    
    /**
     * Envia e-mail com nossas recomendações de empresas.
     * 
     * @param $user             usuário que vai receber
     * @author Lucas Gaspar
     * @since 2012-04-23
     */
    public function someIndicatedCompanies2($user) {
        $view->user = $user;
        $render = $this->_render('some_indicated_companies_2', $view);
        $subject = 'Contatos de empresas de Assessoria Empresarial no Empreendemia';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("luiz@empreendemia.com.br", "Luiz Piovesana");
        $mail->setReplyTo("luiz@empreendemia.com.br", "Luiz Piovesana");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->setSubject($subject);
        
        // envia o e-mail
        return $this->sendEmail($mail, 'indicacao de algumas empresas');
    }
    
    /**
     * Envia e-mail com nossas recomendações de empresas.
     * 
     * @param $user             usuário que vai receber
     * @author Lucas Gaspar
     * @since 2012-04-23
     */
    public function someIndicatedCompanies3($user) {
        $view->user = $user;
        $render = $this->_render('some_indicated_companies_3', $view);
        $subject = 'Contatos de empresas de Publicidade e Propaganda no Empreendemia';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("luiz@empreendemia.com.br", "Luiz Piovesana");
        $mail->setReplyTo("luiz@empreendemia.com.br", "Luiz Piovesana");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->setSubject($subject);
        
        // envia o e-mail
        return $this->sendEmail($mail, 'indicacao de algumas empresas');
    }

    /**
     * Envia e-mail de últimas empresas cadastradas na rede.
     * 
     * @param $user             usuário que vai receber
     * @author Mauro Ribeiro
     * @since 2012-03-12
     */
    public function lastRegisteredCompanies($user) {
        $view->user = $user;
        $render = $this->_render('last_registered_companies', $view);
        $subject = 'Contatos de empresas de '.$user->company->city->name.' que podem te interessar';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("millor@empreendemia.com.br", "Millor Machado");
        $mail->setReplyTo("millor@empreendemia.com.br", "Millor Machado");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->setSubject($subject);
        
        // envia o email
        return $this->sendEmail($mail, 'lista de novas empresas');
    }

    /**
     * Envia e-mail de como melhorar visitas.
     * 
     * @param $user             usuário que vai receber
     * @author Mauro Ribeiro
     * @since 2012-03-12
     */
    public function howToImproveVisits($user) {
        $view->user = $user;
        $render = $this->_render('how_to_improve_visits', $view);
        $subject = 'Quer aumentar as visitas na sua página e vender mais?';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("gabriel@empreendemia.com.br", "Gabriel Costa");
        $mail->setReplyTo("gabriel@empreendemia.com.br", "Gabriel Costa");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->setSubject($subject);
        
        // envia o e-mail
        return $this->sendEmail($mail, 'dicas para melhorar visitas');
    }

    /**
     * Envia e-mail para usuários de empresas com premiums expirados.
     * 
     * @param array(EeBot_Model_Data_Company) $companies    empresas que expiraram
     * @author Mauro Ribeiro
     * @since 2012-03-05
     */
    public function expiringPremium($user) {
        $view->user = $user;
        $render = $this->_render('premium_expiration', $view);
        $subject = 'Como está sendo o seu Premium no Empreendemia?';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("lucas@empreendemia.com.br", "Lucas Hoogerbrugge");
        $mail->setReplyTo("lucas@empreendemia.com.br", "Lucas Hoogerbrugge");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->setSubject($subject);
        
        // envia o email
        return $this->sendEmail($mail, 'premium expiration');
    }

    /**
     * Envia email para sysadmins com lista de ofertas expiradas.
     * 
     * @param array(EeBot_Model_Data_Product) $products     ofertas expiradas
     * @author Mauro Ribeiro
     */
    public function offersExpirations($products) {
        $view->products = $products;
        $render = $this->_render('admins/offers_expirations', $view);
        $subject = 'Cron Diário - Ofertas Expiradas';

        return $this->adminsEmail($subject, $render);
    }


    /**
     * Envia email para sysadmins com lista de anúncios expirados.
     * 
     * @param array(EeBot_Model_Data_Ad) $ads     anúncios expirados
     * @author Mauro Ribeiro
     */
    public function adsExpirations($ads) {
        $view->ads = $ads;
        $render = $this->_render('admins/ads_expirations', $view);
        $subject = 'Cron Diário - Publicidades Expiradas';

        return $this->adminsEmail($subject, $render);
    }


    /**
     * Envia email para usuários sobre o fim da campanha.
     * 
     * @param EeBot_Model_Data_Ad $ad    anúncio expirado
     * @author Mauro Ribeiro
     */
    public function adExpiration($user, $ad) {
        $view->user = $user;
        $view->ad = $ad;
        $render = $this->_render('ad_expiration', $view);
        $subject = 'Relatório da sua campanha de publicidade - Empreendemia';

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($render);
        $mail->setFrom("millor@empreendemia.com.br", "Millor Machado");
        $mail->setReplyTo("millor@empreendemia.com.br", "Millor Machado");
        $mail->addTo($user->login, $user->name.' '.$user->family_name);
        $mail->addBcc("millor@empreendemia.com.br", "Millor Machado");
        $mail->setSubject($subject);
        
        // envia o email
        return $this->sendEmail($mail, 'ad expiration');
    }


    /**
     * Envia email para sysadmins com lista de requisições de serviços expiradas.
     * 
     * @param array(EeBot_Model_Data_Demand) $demands
     * @author Mauro Ribeiro
     */
    public function demandsExpirations($demands) {
        $view->demands = $demands;
        $render = $this->_render('admins/demands_expirations', $view);
        $subject = 'Cron Diário - Serviços Expirados';

        return $this->adminsEmail($subject, $render);
    }


    /**
     * Envia email para sysadmins com o cohort mensal de login.
     * 
     * @author Mauro Ribeiro
     */
    public function loginMonthlyCohort($cohort) {
        $view->cohort = $cohort;
        $render = $this->_render('admins/login_monthly_cohort', $view);
        $subject = 'Cron Mensal - Cohort de Login';

        return $this->adminsEmail($subject, $render);
    }

}
