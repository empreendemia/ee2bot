<?php
/**
 * Sector.php - EeBot_Model_Data_Sector
 * Representação dos dados do setor
 * 
 * @package models
 * @subpackage data
 * @author Lucas Gaspar
 * @since 2012-04
 */

class EeBot_Model_Data_Sector
{
    /**
     * ID do setor
     * @var int 
     */
    public $id;
    /**
     * Nome do setor
     * @var string 
     */
    public $name;
    /**
     * Slug do setor
     * @var string
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
