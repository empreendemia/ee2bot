<?php
/**
 * Cities.php - EeBot_Model_Cities
 * Mapper de cidades
 * 
 * @package models
 * @author Mauro Ribeiro
 * @since 2012-03
 */
class EeBot_Model_Cities 
{
    private $_dbTable;

   /**
    * Construtor da porra toda
    */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Cities();
    }

    /**
     * Procura uma cidade 
     * @param $id_or_slug           id ou slug da cidade
     * @return Ee_Model_Data_City 
     */
    public function find($id_or_slug) {

        if (is_numeric($id_or_slug)) {
            $result = $this->_dbTable->find($id_or_slug);
        }
        else {
            $result = $this->_dbTable->findBySlug($id_or_slug);
        }

        if (0 == count($result)) {
            return;
        }

        $city = new EeBot_Model_Data_City($result->current());
        return $city;
    }
    
}

