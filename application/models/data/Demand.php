<?php

class EeBot_Model_Data_Demand
{
    public $user_id;
    public $sector_id;
    public $title;
    public $slug;
    public $price;
    public $description;
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

