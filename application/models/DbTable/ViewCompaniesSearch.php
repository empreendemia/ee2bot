<?php

class EeBot_Model_DbTable_ViewCompaniesSearch extends Zend_Db_Table_Abstract
{

    protected $_name = 'view_companies_search';
    protected $_primary = 'company_id';

    public function updateTable() {
        $query = '
            CREATE OR REPLACE VIEW view_companies_search as
            SELECT SQL_NO_CACHE
            company.id as company_id,
            concat_ws(" ",sector.name,city.name,region.name,region.symbol,company.name,company.activity,company.address_street,company.description, group_concat(product.name),group_concat(product.description)) as company_text
            FROM companies as company
            JOIN sectors as sector on company.sector_id = sector.id
            JOIN cities as city on company.city_id = city.id
            JOIN regions as region ON city.region_id = region.id
            LEFT JOIN products as product ON product.company_id = company.id
            WHERE company.status = "active"
            GROUP by company.id
        ';
         $this->getAdapter()->query($query);
    }

}

