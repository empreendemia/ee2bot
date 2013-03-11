<?php

class EeBot_Model_Data_Company
{
    public $id;
    public $sector_id;
    public $sector;
    public $city_id;
    public $city;
    public $reputation;
    public $name;
    public $slug;
    public $date_created;
    public $date_updated;
    public $plan;
    public $plan_expiration;
    public $type;
    public $status;
    public $image;
    public $card_image;
    public $side_image;
    public $activity;
    public $description;
    public $phone;
    public $phone2;
    public $email;
    public $website;
    public $address_street;
    public $address_number;
    public $address_complement;
    public $about;
    public $slides_url;
    public $slides_embed;
    public $video_url;
    public $link_blog;
    public $link_youtube;
    public $link_vimeo;
    public $link_slideshare;
    public $link_facebook;
    public $contact_twitter;
    public $contact_skype;
    public $contact_msn;
    public $contact_gtalk;


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

