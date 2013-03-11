<?php

/**
 * Regions.php - EeBot_Model_Regions
 * Mapper de regiões (estados)
 * 
 * @package models
 * @author Mauro Ribeiro
 * @since 2012-03
 */
class EeBot_Model_Regions 
{

    private $_dbTable;

   /**
    * Construtor da porra toda
    */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Regions();
    }
    
    /**
     * Procura os dados de região
     * 
     * @param $id_or_slug              (int)id ou (string)slug da região
     * @return Ee_Model_Data_Region    objeto da região
     * @since 2011-06
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

        $region = new EeBot_Model_Data_Region($result->current());
        return $region;
    }

}

