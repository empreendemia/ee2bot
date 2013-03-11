<?php

class Ee_Model_DbTable_Sector extends Zend_Db_Table_Abstract
{

    protected $_name = 'sectors';

    public function findBySlug($slug)
    {
        $where = $this->getAdapter()->quoteInto('slug = ?', $slug);
        return $this->fetchAll($where, 'slug');
    }

}

