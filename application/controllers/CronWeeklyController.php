<?php
/**
 * CronMonthlyController.php
 * 
 * Controller de envio de e-mails que é executado semanalmente.
 * 
 * @author Lucas Gaspar
 * @since 2012-04-16
 */

class CronWeeklyController extends Zend_Controller_Action
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
     * E-mail que avisa usuário com notificações pendentes de 14 a 22 dias atrás.
     * Lifecycle #: 1
     *  
     * @author Lucas Gaspar
     * @since 2012-04-16
     */
    public function exchangedCardsInFifteenDaysAgoAction()
    {
        $user_mapper = new EeBot_Model_Users();
        /* Solicitação de usuário com notificações pendentes' */
        $users = $user_mapper->exchangedCardsInFifteenDaysAgoAction();
    
        /* Só envia e-mail se ele já não foi mandando ao usuário alguma vez */
        if ($users) {
            foreach ($users as $user) {
                if ($user->lifecycle[1] == 0){
                    $user->lifecycle[1] = $user->lifecycle[1] + 1;
                    $user_mapper->save($user);
                    $this->_helper->EeMsg->pendingNotifications($user);
                }
            }
        }
        
        die('exchangedCardsInFifteenDaysAgo');
    }
    
}
