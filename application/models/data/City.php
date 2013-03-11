<?php
/**
 * City.php - EeBot_Model_Data_City
 * Representação dos dados de uma cidade
 * 
 * @package models
 * @subpackage data
 * @author Mauro Ribeiro
 * @since 2012-03
 */

class EeBot_Model_Data_City
{
    /**
     * ID da cidade
     * @var int
     */
    public $id;
    /**
     * ID da região (estado) da cidade
     * @var int
     */
    public $region_id;
    /**
     * Região (estado) da cidade
     * @var Ee_Model_Data_Region
     */
    public $region;
    /**
     * Nome da região (estado)
     * @var string
     * @example "Sao Paulo"
     */
    public $name;
    /**
     * Slug da cidade
     * @var string
     * @example "sao-paulo"
     */
    public $slug;


    public function  __construct($data = null) {
        if ($data) $this->set($data);
    }

    public function set($data) {
        foreach($data as $id => $value) {
            if (property_exists($this,$id)) {
                $this->$id = $value;
            }
        }
    }

}

