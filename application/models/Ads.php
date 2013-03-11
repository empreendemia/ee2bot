<?php
/**
 * Ads.php - Ee_Model_Ads
 * Mapper de anúncios
 * 
 * @package models
 * @author Mauro Ribeiro
 * @since 2011-09-06
 */
class EeBot_Model_Ads
{

    private $_dbTable;

   /**
    * Construtor da porra toda
    */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Ads();
    }

    /**
     * Procura por anúncios expirados e atualiza o status delas
     * @return array(EeBot_Model_Data_Ad)   anúncios expirados 
     * @author Mauro Ribeiro
     */
    public function expirations() {

        // procura anúncios marcados como ativos mas que já expiraram
        $select = $this->_dbTable
                ->select()
                ->where('status = ?', 'active')
                ->where('date_deadline < CURDATE()');

        $rows = $this->_dbTable->fetchAll($select);

        $ads = array();
        foreach ($rows as $row) {
            $ad = new EeBot_Model_Data_Ad($row);
            $ads[] = $ad;
        }

        // seta todos os anúncios ativos que já passaram do prazo como inativos
        $data = array();
        $data['status'] = 'inactive';
        $this->_dbTable->update(
            $data,
            array(
                'status = ?' => 'active',
                'date_deadline < NOW()'
            )
        );

        return $ads;
    }
}

