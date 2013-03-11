<?php
/**
 * User.php - EeBot_Model_Data_User
 * Representação dos dados do usuário
 * 
 * @package models
 * @subpackage data
 * @author Mauro Ribeiro
 * @since 2012-09
 */

class EeBot_Model_Data_User
{
    public $id;
    public $company_id;
    public $company;
    public $login;
    public $password;
    public $group;
    public $date_created;
    public $date_updated;
    public $options = '1';
    public $mails = '1111';
    public $name;
    public $family_name;
    public $image;
    public $description;
    public $job;
    public $phone;
    public $cell_phone;
    public $email;
    public $lifecycle = '0000000000';

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

