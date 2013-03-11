<?php
/**
 * Region.php - EeBot_Model_Data_Region
 * Representação dos dados da região (estado)
 * 
 * @package models
 * @subpackage data
 * @author Mauro Ribeiro
 * @since 2012-03
 */

class EeBot_Model_Data_Region
{
    /**
     * ID da região (estado)
     * @var int
     */
    public $id;
    /**
     * ID do país em que a região se encontra
     * @var int
     */
    public $country_id;
    /**
     * País em que a região se encontra
     * @var int
     */
    public $country;
    /**
     * Sigla do estado
     * @var string
     * @example "SP"
     */
    public $symbol;
    /**
     * Nome do estado
     * @var string
     * @example "São Paulo"
     */
    public $name;
    /**
     * Slug do estado
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

