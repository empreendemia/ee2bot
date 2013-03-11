<?php
/**
 * CleanerController.php - CleanerController
 * Limpa a sujeira gerada pelo sistema ou pelo usuário no banco de dados
 * 
 * @author Mauro Ribeiro
 * @since 2011-09-16
 */
class CleanerController extends Zend_Controller_Action
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

        if ($ip != '127.0.0.1' && $ip != '74.200.74.193') {
            $this->_redirect('http://www.empreendemia.com.br');
        }
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * Limpa a sujeira deixada pelo Sr Wilson (CAPSLOCK)
     * @author Mauro Ribeiro
     */
    public function mrWilsonTracesKillerAction()
    {
        require(APPLICATION_PATH.'/../library/filters/MrWilsonTracesKiller.php');
        $name_filter = new EeBot_Filter_MrWilsonTracesKiller(array('type'=>'name', 'capslock'=>false));
        $title_filter = new EeBot_Filter_MrWilsonTracesKiller(array('type'=>'title'));
        $text_filter = new EeBot_Filter_MrWilsonTracesKiller(array('type'=>'text'));

        $company_mapper = new EeBot_Model_Companies();
        $companies = $company_mapper->findMrWilson();
        $this->view->companies = $companies;
        $fixed_companies = array();
        foreach ($companies as $id => $company) {
            $fixed_companies[$id]->id = $company->id;
            $fixed_companies[$id]->name = $title_filter->filter($company->name);
            $fixed_companies[$id]->activity = $title_filter->filter($company->activity);
            $fixed_companies[$id]->description = $text_filter->filter($company->description);
            $fixed_companies[$id]->about = $text_filter->filter($company->about);
        }
        $this->view->fixed_companies = $fixed_companies;

        $user_mapper = new EeBot_Model_Users();
        $users = $user_mapper->findMrWilson();
        $this->view->users = $users;
        $fixed_users = array();
        foreach ($users as $id => $user) {
            $fixed_users[$id]->id = $user->id;
            $fixed_users[$id]->name = $name_filter->filter($user->name);
            $fixed_users[$id]->family_name = $name_filter->filter($user->family_name);
            $fixed_users[$id]->description = $text_filter->filter($user->description);
            $fixed_users[$id]->job = $title_filter->filter($user->job);
        }
        $this->view->fixed_users = $fixed_users;

        $product_mapper = new EeBot_Model_Products();
        $products = $product_mapper->findMrWilson();
        $this->view->products = $products;
        $fixed_products = array();
        foreach ($products as $id => $product) {
            $fixed_products[$id]->id = $product->id;
            $fixed_products[$id]->name = $title_filter->filter($product->name);
            $fixed_products[$id]->description = $text_filter->filter($product->description);
            $fixed_products[$id]->about = $text_filter->filter($product->about);
        }
        $this->view->fixed_products = $fixed_products;

        if (isset($_GET['kill']) && $_GET['kill'] == 'fuckyeah') {
            foreach ($fixed_companies as $company) {
                $company_mapper->save($company);
            }
            foreach ($fixed_users as $user) {
                $user_mapper->save($user);
            }
            foreach ($fixed_products as $product) {
                $product_mapper->save($product);
            }
            $this->view->killed = true;
        }
        else {
            $this->view->killed = false;
        }

    }


}



