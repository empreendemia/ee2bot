<?php
/**
 * Users.php - EeBot_Model_Users
 * Mapper de usuários
 * 
 * @package models
 * @author Mauro Ribeiro
 * @since 2011-09-06
 */

class EeBot_Model_Users
{

    private $_dbTable;
    
    /**
     * Constrói a porra toda
     */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Users();
    }

    /**
     * Transforma um objeto em um array de dados
     * 
     * @param object $object    objeto a ser transformado
     * @return array            objeto transformado em array
     * @author Mauro Ribeiro
     */
    private function _dataArray($object) {
        $data = array();
        foreach ($object as $index => $value) {
            if (is_object($value) == false && $value != null) {
                $data[$index] = $value;
            }
        }
        return $data;
    }

    /**
     * Salva os dados do usuário
     * @param object $object
     * @author Mauro Ribeiro
     */
    function save($object) {
        // atualiza
        if (isset($object->id)) {
            $save = $this->_dbTable->update($this->_dataArray($object), array('id = ?' => $object->id));
        }
        // insere
        else {
            $save = $this->_dbTable->insert($this->_dataArray($object));
            $object->id = $this->_dbTable->getAdapter()->lastInsertId();
        }
        return $save;
    }

    /**
     * Procura os dados de um usuário
     * @param int $user_id
     * @return EeBot_Model_Data_User 
     * @author Mauro Ribeiro
     */
    public function find($user_id) {

        $result = $this->_dbTable->find($user_id);

        if (0 == count($result)) return false;
        
        $user = new EeBot_Model_Data_User($result->current());
        return $user;
    }
    
    /**
     * Procura usuários com ao menos um contato pendente feito há mais de 15 dias
     * @param array $options            opções da query
     * @param string $options['where']  filtro 
     * @return array(EeBot_Model_Data_User)
     * @author Lucas Gaspar
     * @since 2012-04-16
     */
    public function exchangedCardsInFifteenDaysAgoAction() {
        $select = $this->_dbTable
            ->select()
            ->from($this->_dbTable)
            ->join('contacts', 'contacts.contact_id = users.id', null)
            ->where('contacts.date < DATE_SUB(NOW(), INTERVAL 14 DAY)')
            ->where('contacts.date >= DATE_SUB(NOW(), INTERVAL 22 DAY)')
            ->where('
                contacts.id NOT IN (

                    SELECT DISTINCT (
                    contact_1.id
                    )
                    FROM contacts AS contact_1
                    JOIN contacts AS contact_2 ON ( 
                        contact_1.user_id = contact_2.contact_id
                        AND contact_1.contact_id = contact_2.user_id
                    )

                )
            ')   
            ->group('users.id')
            ->setIntegrityCheck(false);
        
        //die($select->__toString());
        
        $rows = $this->_dbTable->fetchAll($select);
        
        if (0 == count($rows)) return null;
        
        $users = array();
        $company_mapper = new EeBot_Model_Companies();
        foreach ($rows as $row) {
            $user = new EeBot_Model_Data_User($row);
            $user->company = $company_mapper->find($user->company_id);
            $users[] = $user;
        }
        
        return $users;
    }
    
    /**
     * Procura usuários que logaram 15 dias atrás (por estado)
     * @param array $options            opções da query
     * @param string $options['where']  filtro 
     * @return array(EeBot_Model_Data_User)
     * @author Lucas Gaspar
     * @since 2012-04-04
     */
    public function loggedInFifteenDaysAgoRegions($regions = null, $sectors = null) {
        $select = $this->_dbTable
            ->select()
            ->from($this->_dbTable)
            ->where('users.date_updated < DATE_SUB(NOW(), INTERVAL 15 DAY)')
            ->where('users.date_updated >= DATE_SUB(NOW(), INTERVAL 16 DAY)')
            ->group('users.id')
            ->setIntegrityCheck(false);
        
        if (($regions != null) || ($sectors != NULL))
            $select->join('companies', 'users.company_id = companies.id', null);
        
        if ($regions) {
            $select->join('cities', 'companies.city_id = cities.id', null);
            $select->where('cities.region_id IN ('.implode(',', $regions).')');
        }
        
        if ($sectors) {
            $select->where('companies.sector_id IN ('.implode(',', $sectors).')');
        }
        
        $rows = $this->_dbTable->fetchAll($select);
        
        if (0 == count($rows)) return null;
        
        $users = array();
        $company_mapper = new EeBot_Model_Companies();
        foreach ($rows as $row) {
            $user = new EeBot_Model_Data_User($row);
            $user->company = $company_mapper->find($user->company_id);
            $users[] = $user;
        }
        
        return $users;
    }
    
    /**
     * Procura usuários que logaram 15 dias atrás (por cidade)
     * @param array $options            opções da query
     * @param string $options['where']  filtro 
     * @return array(EeBot_Model_Data_User)
     * @author Lucas Gaspar
     * @since 2012-04-23
     */
    public function loggedInFifteenDaysAgoCities($cities = null, $sectors = null) {
        $select = $this->_dbTable
            ->select()
            ->from($this->_dbTable)
            ->where('users.date_updated < DATE_SUB(NOW(), INTERVAL 15 DAY)')
            ->where('users.date_updated >= DATE_SUB(NOW(), INTERVAL 16 DAY)')
            ->group('users.id')
            ->setIntegrityCheck(false);
        
        if (($cities != null) || ($sectors != NULL))
            $select->join('companies', 'users.company_id = companies.id', null);
        
        if ($cities) {
            $select->where('companies.city_id IN ('.implode(',', $cities).')');
        }
        
        if ($sectors) {
            $select->where('companies.sector_id IN ('.implode(',', $sectors).')');
        }
        
        $rows = $this->_dbTable->fetchAll($select);
        
        if (0 == count($rows)) return null;
        
        $users = array();
        $company_mapper = new EeBot_Model_Companies();
        foreach ($rows as $row) {
            $user = new EeBot_Model_Data_User($row);
            $user->company = $company_mapper->find($user->company_id);
            $users[] = $user;
        }
        
        return $users;
    }
    
    /**
     * Procura usuários que logaram 30 dias atrás
     * @param array $options            opções da query
     * @param string $options['where']  filtro 
     * @return array(EeBot_Model_Data_User)
     * @author Mauro Ribeiro
     * @since 2012-03-12
     */
    public function loggedInOneMonthAgo($cities = null) {
        $select = $this->_dbTable
            ->select()
            ->from($this->_dbTable)
            ->where('users.date_updated < DATE_SUB(NOW(), INTERVAL 30 DAY)')
            ->where('users.date_updated >= DATE_SUB(NOW(), INTERVAL 31 DAY)')
            ->group('users.id')
            ->setIntegrityCheck(false);
        
        if ($cities) {
            $select->join('companies', 'users.company_id = companies.id', null);
            $select->where('companies.city_id IN ('.implode(',', $cities).')');
        }
        
        $rows = $this->_dbTable->fetchAll($select);
        
        if (0 == count($rows)) return null;
        
        $users = array();
        $company_mapper = new EeBot_Model_Companies();
        foreach ($rows as $row) {
            $user = new EeBot_Model_Data_User($row);
            $user->company = $company_mapper->find($user->company_id);
            $users[] = $user;
        }
        
        return $users;
    }

    /**
     * Procura usuários que se cadastraram 15 dias atrás
     * @return array(EeBot_Model_Data_User)
     * @author Mauro Ribeiro
     * @since 2012-03-12
     */
    public function signedUpFifteenDaysAgo() {
        $select = $this->_dbTable
            ->select()
            ->from($this->_dbTable)
            ->where('users.date_created = CAST(DATE_SUB(NOW(), INTERVAL 15 DAY) AS DATE)')
            ->group('users.id');
        
        $rows = $this->_dbTable->fetchAll($select);
        
        if (0 == count($rows)) return null;
        
        $users = array();
        foreach ($rows as $row) {
            $user = new EeBot_Model_Data_User($row);
            $users[] = $user;
        }
        
        return $users;
    }
    

    /**
     * Procura por membros de uma empresa
     * @param Ee_Model_Data_Company $company        empresa escolhida
     * @return array(EeBot_Model_Data_User)         membros da empresa
     */
    public function findByCompany($company) {

        $rows = $this->_dbTable->findByCompanyId($company->id);

        if ($rows) {
            $users = array();
            foreach ($rows as $row) {
                $user = new EeBot_Model_Data_User($row);
                $user->company = $company;
                $users[] = $user;
            }
        }
        else
            return false;

        return $users;
    }

    /**
     * Procura por rastros de Sr. Wilson nos usuários
     * @return array(EeBot_Model_Data_User)
     * @author Mauro Ribeiro
     */
    public function findMrWilson() {
        $select = $this->_dbTable
            ->select()
            ->where('
                (upper(name) = (name COLLATE utf8_bin ))
                OR
                (upper(family_name) = (family_name COLLATE utf8_bin ))
                OR
                (upper(description) = (description COLLATE utf8_bin ) AND length(description) > 0)
                OR
                (upper(job) = (job COLLATE utf8_bin ) AND length(job) > 0)
            ');

        $rows = $this->_dbTable->fetchAll($select);

        $users = array();

        foreach ($rows as $row) {
            $user = new EeBot_Model_Data_User();
            $user->set($row);
            $users[] = $user;
        }

        return $users;
    }

    
    
    /**
     * Atualiza o cohort de login mensal
     * @return array    cohort de login mensal
     * @author Mauro Ribeiro 
     */
    public function loginMonthlyCohort() {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/ee.ini', 'production');
        $metrics_folder = $config->files->metrics;

        // abre
        $row = 1;
        $cohort = array();
        $handle = fopen($metrics_folder.'cohort.csv', 'r');
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $cohort[] = $data;
        }

        fclose($handle);

        // faz a manipulação
        $now_date = date('Y-m');
        $past_date = date('Y-m', strtotime('-2 days'));
        $cohort[0][] = $past_date;
        $num = count($cohort);

        if ($num > 1) {
            // para cada ano e mês (YYYY-MM) 
            for ($i = 1; $i < $num; $i++) {
                $date_created_min = $cohort[$i][0].'-01';
                $date_created_max = $cohort[$i][0].'-31';
                
                // pessoas que logaram no último mês e que se cadastraram num
                // mês específico (YYYY-MM-01 a YYYY-MM-31)
                $select = $this->_dbTable
                    ->select()
                    ->from($this->_dbTable, array('COUNT(*) as count'))
                    ->where('date_updated < NOW()')
                    ->where('date_updated >= DATE_SUB(NOW(), INTERVAL 1 MONTH)')
                    ->where('date_created < ?', $date_created_max)
                    ->where('date_created >= ?', $date_created_min);
                $count = $this->_dbTable->fetchRow($select)->count;

                // total de pessoas que se cadastraram naquele mês
                $select = $this->_dbTable
                    ->select()
                    ->from($this->_dbTable, array('COUNT(*) as count'))
                    ->where('date_created < ?', $date_created_max)
                    ->where('date_created >= ?', $date_created_min);
                $total = $this->_dbTable->fetchRow($select)->count;
                
                // logins no último mês dividido por total de cadastros no mês
                $percent = ($count/$total) * 100;
                $cohort[$i][$num] = round($percent * 100) / 100;
            }
        }

        $new_row[] = $past_date;
        if ($num > 1) for ($i = 1; $i < $num; $i++) $new_row[] = 0;
        $new_row[] = 100;

        $cohort[] = $new_row;

        // salva
        $handle = fopen($metrics_folder.'cohort.csv', 'w');

        $csv = array();
        foreach ($cohort as $id => $row) {
            $csv[] = array_merge($row);
        }
        foreach ($csv as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $cohort;
    }
}
