<?php

class EeBot_Model_Data_Product
{
    public $id;
    public $company_id;
    public $slug;
    public $name;
    public $date_created;
    public $date_updated;
    public $description;
    public $website;
    public $image;
    public $sort;
    public $about;
    public $image_1;
    public $image_2;
    public $image_3;
    public $image_4;
    public $image_5;
    public $subtitle_1;
    public $subtitle_2;
    public $subtitle_3;
    public $subtitle_4;
    public $subtitle_5;
    public $offer_status;
    public $offer_description;
    public $offer_date_created;
    public $offer_date_deadline;

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

