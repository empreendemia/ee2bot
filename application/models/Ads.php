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

    /**
     * Lista das cidades de um anúncio
     * @return array(EeBot_Model_Data_Ad_City)   lista de cidades
     * @author Mauro Ribeiro
     */
    public function cities($ad, $order = array('clicks DESC', 'views DESC')) {
        // aceita o objeto ou o id
        if (is_object($ad)) $ad_id = $ad->id;
        else $ad_id = $ad;
        
        $ad_city_table = new EeBot_Model_DbTable_AdsCities();
        $city_mapper = new EeBot_Model_Cities();
        $region_mapper = new EeBot_Model_Regions();
        
        // procura as cidades
        $select = $ad_city_table
                ->select()
                ->where('ad_id = '.$ad)
                ->order($order)
                ->setIntegrityCheck(false);
        
        $rows = $ad_city_table->fetchAll($select);
        $ads_cities = array();
        foreach ($rows->toArray() as $row) {
            $data = (object) $row;
            $data->city = $city_mapper->find($data->city_id);
            $data->city->region = $region_mapper->find($data->city->region_id);
            $ads_cities[] = $data;
        }
        
        return $ads_cities;
    }

    /**
     * Lista dos setores de um anúncio
     * @return array(EeBot_Model_Data_Ad_Sector)   lista de setores
     * @author Mauro Ribeiro
     */
    public function sectors($ad, $order = array('clicks DESC', 'views DESC')) {
        // aceita o objeto ou o id
        if (is_object($ad)) $ad_id = $ad->id;
        else $ad_id = $ad;
        
        $ad_sector_table = new EeBot_Model_DbTable_AdsSectors();
        $sector_mapper = new EeBot_Model_Sectors();
        
        // procura os setores
        $select = $ad_sector_table
                ->select()
                ->where('ad_id = '.$ad)
                ->order($order)
                ->setIntegrityCheck(false);
        
        $rows = $ad_sector_table->fetchAll($select);
        $ads_sectors = array();
        foreach ($rows->toArray() as $row) {
            $data = (object) $row;
            $data->sector = $sector_mapper->find($data->sector_id);
            $ads_sectors[] = $data;
        }
        
        return $ads_sectors;
    }
}

