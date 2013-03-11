<?php
/**
 * Demands.php - Ee_Model_Demands
 * Mapper das requisições de serviços
 * 
 * @package models
 * @author Mauro Ribeiro
 * @since 2011-09-06
 */
class EeBot_Model_Demands
{

    private $_dbTable;

    /**
     * Constrói a porra toda
     */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Demands();
    }

    /**
     * Procura por requisições de serviços que expiraram e atualiza o status
     * @return array(EeBot_Model_Data_Demand)
     * @author Mauro Ribeiro 
     */
    public function expirations() {

        // procura requisições que estão ativas mas já expiraram
        $select = $this->_dbTable
                ->select()
                ->where('status = ?', 'active')
                ->where('date_deadline < NOW()');

        $rows = $this->_dbTable->fetchAll($select);

        $demands = array();
        foreach ($rows as $row) {
            $demand = new EeBot_Model_Data_Demand($row);
            $demands[] = $demand;
        }

        // atualiza como inativas
        $data = array();
        $data['status'] = 'inactive';
        $this->_dbTable->update(
            $data,
            array(
                'status = ?' => 'active',
                'date_deadline < NOW()'
            )
        );

        return $demands;
    }

}

