<?php
/**
 * Companies.php - Ee_Model_Companies
 * Mapper de empresas
 * 
 * @package models
 * @author Mauro Ribeiro
 * @since 2011-09-06
 */
class EeBot_Model_Companies
{

    private $_dbTable;
    
    /**
     * Construtor da porra toda
     */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Companies();
    }
    
    /**
     * Transforma um objeto em um array de dados
     * 
     * @param $object   objeto a ser transformado
     * @return array    objeto transformado em array
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
     * Método padrão de salvar uma tupla no banco de dados
     * 
     * @param Ee_Model_Data $model     objeto a ser salvo
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
     * Procura os dados de uma empresa à partir de seu id
     * @param int $company_id
     * @return EeBot_Model_Data_Company 
     * @author Mauro Ribeiro
     */
    public function find($company_id) {
        
        $result = $this->_dbTable->find($company_id);

        if (0 == count($result)) return false;
        
        $cities_mapper = new EeBot_Model_Cities();
        $regions_mapper = new EeBot_Model_Regions();
        
        $company = new EeBot_Model_Data_Company($result->current());
        $company->city = $cities_mapper->find($company->city_id);
        $company->city->region = $regions_mapper->find($company->city->region_id);
        return $company;
    }

    /**
     * Procura premiums expirados e atualiza o status
     * @return array(EeBot_Model_Data_Company)
     * @author Mauro Ribeiro
     */
    public function premiumsExpirations() {

        // procura empresas que são premium mas já expiraram
        $select = $this->_dbTable
                ->select()
                ->where('plan = ?', 'premium')
                ->where('plan_expiration < NOW()');

        $rows = $this->_dbTable->fetchAll($select);

        $companies = array();
        foreach ($rows as $row) {
            $company = new EeBot_Model_Data_Company($row);
            $companies[] = $company;
        }
        
        // volta as empresas para o grátis
        $data = array();
        $data['plan'] = 'gratis';
        $this->_dbTable->update(
            $data,
            array(
                'plan = ?' => 'premium',
                'plan_expiration < NOW()'
            )
        );

        return $companies;
    }

    /**
     * Procura premiums que estão para expirar em dois dias
     * @return array(EeBot_Model_Data_Company)
     * @author Mauro Ribeiro
     */
    public function expiringPremiums() {

        // procura empresas que estão para expirar em dois dias
        $select = $this->_dbTable
                ->select()
                ->where('plan = ?', 'premium')
                ->where('plan_expiration >= DATE_ADD(NOW(), INTERVAL 1 DAY)')
                ->where('plan_expiration < DATE_ADD(NOW(), INTERVAL 2 DAY)');

        $rows = $this->_dbTable->fetchAll($select);
        
        $companies = array();
        foreach ($rows as $row) {
            $company = new EeBot_Model_Data_Company($row);
            $companies[] = $company;
        }

        return $companies;
    }

    /**
     * Atualiza tabela de busca
     * @author Mauro Ribeiro
     */
    public function updateSearch() {
        $view_companies_search = new EeBot_Model_DbTable_ViewCompaniesSearch();
        $view_companies_search->updateTable();
    }

    /**
     * Procura por rastros de Sr. Wilson nas empresas
     * @return array(EeBot_Model_Data_Company)
     * @author Mauro Ribeiro
     */
    public function findMrWilson() {
        $select = $this->_dbTable
            ->select()
            ->where('
                (upper(name) = (name COLLATE utf8_bin ) AND (length(name) - length(replace(name," ","")) > 1))
                OR
                (upper(description) = (description COLLATE utf8_bin ) AND length(description) > 0)
                OR
                (upper(activity) = (activity COLLATE utf8_bin ) AND length(activity) > 0)
                OR
                (upper(about) = (about COLLATE utf8_bin ) AND length(about) > 0)
            ');

        $rows = $this->_dbTable->fetchAll($select);

        $companies = array();

        foreach ($rows as $row) {
            $company = new EeBot_Model_Data_Company();
            $company->set($row);
            $companies[] = $company;
        }

        return $companies;
    }

}

