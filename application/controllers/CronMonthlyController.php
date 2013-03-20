<?php
/**
 * CronMonthlyController.php
 * 
 * Controller de envio de e-mails que é executado mensalmente.
 * 
 * @author Mauro Ribeiro
 */

class CronMonthlyController extends Zend_Controller_Action
{

    public function init()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		  $ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
		  $ip = $_SERVER['REMOTE_ADDR'];
		}

        if (strpos($ip, '192.168.33') === false && $ip != '127.0.0.1' && $ip != '74.200.74.193') {
            $this->_redirect('http://www.empreendemia.com.br');
        }
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * Atualiza login de cohort mensal
     * 
     * @author Mauro Ribeiro
     */
    public function loginCohortAction()
    {
        $user_mapper = new EeBot_Model_Users();

        $cohort = $user_mapper->loginMonthlyCohort();

        $sent = $this->_helper->EeMsg->loginMonthlyCohort($cohort);
        
        die('loginCohort');
    }

}
