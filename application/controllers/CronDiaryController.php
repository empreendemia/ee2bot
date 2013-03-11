<?php
/**
 * CronDiaryController.php
 * 
 * Controller de envio de e-mails que é executado diariamente.
 * 
 * @author Mauro Ribeiro
 */

class CronDiaryController extends Zend_Controller_Action
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
     * E-mail de recomendação para empresas que não logam há 15 dias. 
     * Lifecycle #: 0 (Industria -> Engenharia)
     * Lifecycle #: 2 (Assessoria Empresarial -> TI e Informática)
     * Lifecycle #: 3 (Publicidade e Propaganda -> Design)
     * 
     * @author Lucas Gaspar
     * @since 2012-04-04
     */
    public function loggedInFifteenDaysAgoAction()
    {
        $user_mapper = new EeBot_Model_Users();
        
        /** 
         * Lifecycle #0: indicar industrias para engenharia        
         *
         * Usuários de empresas do 'setor de Engenharia' de todos os estados 
         * do Brasil.
         */
        $users = $user_mapper->loggedInFifteenDaysAgoRegions(array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27), array(26));
        
        /* Só envia e-mail se ele já não foi mandando ao usuário alguma vez */
        if ($users) {
            foreach ($users as $user) {
                if ($user->lifecycle[0] == 0){
                    $user->lifecycle[0] = 1;
                    $user_mapper->save($user);
                    $this->_helper->EeMsg->someIndicatedCompanies1($user);
                }
            }
        }
        
        /**
         * Lifecycle #2: indicar assessoria empresarial para TI
         * 
         * Usuários de empresas do 'setor de TI e Informática' das cidades de: 
         * São Paulo, Curitiba, Rio, Campinas, Belo Horizonte, Porto Alegre, 
         * Brasília, Florianópolis, Salvador, Fortaleza, Recife, Goiânia, 
         * São Bernardo do Campo, Guarulhos, Santo André
         */
        $users = $user_mapper->loggedInFifteenDaysAgoCities(array(9422,5915,6861,8781,2700,7777,1724,8213,981,1320,5333,2120,9394,8938,9379), array(19));
        
        /* Só envia e-mail se ele já não foi mandando ao usuário alguma vez */
        if ($users) {
            foreach ($users as $user) {
                if ($user->lifecycle[2] == 0){
                    $user->lifecycle[2] = 1;
                    $user_mapper->save($user);
                    $this->_helper->EeMsg->someIndicatedCompanies2($user);
                }
            }
        }
        
        /**
         * Lifecycle #3: indicar publicidade de propaganda para design
         * 
         * Usuários de empresas do 'setor de design' das cidades de:
         * São Paulo, Curitiba, Rio, Campinas, Belo Horizonte, Porto Alegre, 
         * Brasília, Florianópolis, Salvador, Fortaleza, Recife, Goiânia, 
         * São Bernardo do Campo, Guarulhos, Santo André
         */
        $users = $user_mapper->loggedInFifteenDaysAgoCities(array(9422,5915,6861,8781,2700,7777,1724,8213,981,1320,5333,2120,9394,8938,9379), array(25));
        
        /* Só envia e-mail se ele já não foi mandando ao usuário alguma vez */
        if ($users) {
            foreach ($users as $user) {
                if ($user->lifecycle[3] == 0){
                    $user->lifecycle[3] = 1;
                    $user_mapper->save($user);
                    $this->_helper->EeMsg->someIndicatedCompanies3($user);
                }
            }
        }
        
        die();
    }

    /**
     * E-mail de empresas novas na cidade
     * 
     * @author Mauro Ribeiro
     * @since 2012-03-12
     */
    public function loggedInOneMonthAgoAction()
    {
        $user_mapper = new EeBot_Model_Users();
        /**
         * Usuários de São Paulo, Curitiba, Rio, Campinas, Belo Horizonte,
         * Porto Alegre, Brasília, Florianópolis, Salvador, Fortaleza, Recife,
         * Goiânia, São Bernardo do Campo, Guarulhos, Santo André
         */
        $users = $user_mapper->loggedInOneMonthAgo(array(9422,5915,6861,8781,2700,7777,1724,8213,981,1320,5333,2120,9394,8938,9379));
    
        if ($users) {
            foreach ($users as $user) {
                $this->_helper->EeMsg->lastRegisteredCompanies($user);
            }
        }
        
        die();
    }
    
    /**
     * E-mail de como melhorar visitas
     * 
     * @author Mauro Ribeiro
     * @since 2012-03-12
     */
    public function signedUpFifteenDaysAgoAction()
    {
        $user_mapper = new EeBot_Model_Users();
        $users = $user_mapper->signedUpFifteenDaysAgo();
        
        if ($users) {
            foreach ($users as $user) {
                $this->_helper->EeMsg->howToImproveVisits($user);
            }
        }
        
        die();
    }

    /**
     * Verifica e atualiza empresas com premium expirado
     * @author Mauro Ribeiro
     */
    public function premiumsExpirationsAction()
    {
        $user_mapper = new EeBot_Model_Users();
        $company_mapper = new EeBot_Model_Companies();
        $companies = $company_mapper->premiumsExpirations();

        foreach ($companies as $company) {
            $company->users = $user_mapper->findByCompany($company);
        }
        
        $sent = $this->_helper->EeMsg->adminPremiumsExpirations($companies);

        die();
    }
    
    
    /**
     * Verifica premiums que vão expirar
     *
     *  @author Mauro Ribeiro
     */
    public function expiringPremiumsAction()
    {
        $user_mapper = new EeBot_Model_Users();
        $company_mapper = new EeBot_Model_Companies();
        $companies = $company_mapper->expiringPremiums();

        foreach ($companies as $company) {
            $company->users = $user_mapper->findByCompany($company);
            $this->_helper->EeMsg->expiringPremium($company->users[0]);
        }
        
        $this->_helper->EeMsg->adminExpiringPremiums($companies);

        die();
    }


    /**
     * Verifica e atualiza ofertas expiradas
     * 
     * @author Mauro Ribeiro
     */
    public function offersExpirationsAction()
    {
        $user_mapper = new EeBot_Model_Users();
        $company_mapper = new EeBot_Model_Companies();
        $product_mapper = new EeBot_Model_Products();
        $products = $product_mapper->offersExpirations();

        foreach ($products as $product) {
            $product->company = $company_mapper->find($product->company_id);
            $product->company->users = $user_mapper->findByCompany($product->company);
        }

        $sent = $this->_helper->EeMsg->offersExpirations($products);

        die();
    }

    /**
     * Verifica e atualiza anúncios expirados
     * 
     * @author Mauro Ribeiro
     */
    public function adsExpirationsAction()
    {
        $user_mapper = new EeBot_Model_Users();
        $company_mapper = new EeBot_Model_Companies();
        $product_mapper = new EeBot_Model_Products();
        $ad_mapper = new EeBot_Model_Ads();
        $ads = $ad_mapper->expirations();

        foreach ($ads as $ad) {
            $ad->product = $product_mapper->find($ad->product_id);
            $ad->product->company = $company_mapper->find($ad->product->company_id);
            $ad->product->company->users = $user_mapper->findByCompany($ad->product->company);
        }

        $sent = $this->_helper->EeMsg->adsExpirations($ads);

        die();
    }

    /**
     * Verifica e atualiza requisições de serviços expiradas
     * 
     * @author Mauro Ribeiro
     */
    public function demandsExpirationsAction()
    {
        $user_mapper = new EeBot_Model_Users();
        $company_mapper = new EeBot_Model_Companies();
        $demand_mapper = new EeBot_Model_Demands();
        $demands = $demand_mapper->expirations();

        foreach ($demands as $demand) {
            $user = $user_mapper->find($demand->user_id);
            $demand->company = $company_mapper->find($user->company_id);
            $demand->company->user = $user;
        }

        $sent = $this->_helper->EeMsg->demandsExpirations($demands);

        die();
    }

    /**
     * Atualiza tabela view do índice de busca de empresas
     * 
     * @author Mauro Ribeiro
     */
    public function updateCompaniesSearchAction()
    {
        $company_mapper = new EeBot_Model_Companies();
        $company_mapper->updateSearch();
        die();
    }
}
