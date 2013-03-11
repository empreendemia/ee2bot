<?php

class EeBot_Model_DbTable_Users extends Zend_Db_Table_Abstract
{

    protected $_name = 'users';

    public function findByCompanyId($company_id) {
        $select = $this->select()
                ->where('company_id = ?', $company_id);
        $rows = $this->fetchAll($select);

        if (count($rows) == 0) return false;

        return $rows;
    }

}

