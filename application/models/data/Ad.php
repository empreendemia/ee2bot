<?php

class EeBot_Model_Data_Ad
{
    public $id;
    public $product_id;
    public $status;
    public $date_created;
    public $date_deadline;

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

