<?php
/**
 * Sectors.php - EeBot_Model_Sectors
 * Mapper de setores
 * 
 * @package models
 * @author Lucas Gaspar
 * @since 2012-04
 */

class EeBot_Model_Sectors
{
    private $_dbTable;

    /**
    * Construtor da porra toda
    */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Sectors();
    }    
    
    /**
     * Procura um setor
     * 
     * @param $id_or_slug              (int)id ou (string)slug do setor
     * @return Ee_Model_Data_Sector    objeto do setor
     * @author Mauro Ribeiro
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

        $sector = new EeBot_Model_Data_Sector($result->current());
        return $sector;
    }

}
