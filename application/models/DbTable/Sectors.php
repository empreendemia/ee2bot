<?php

class EeBot_Model_DbTable_Sectors extends Zend_Db_Table_Abstract
{

    protected $_name = 'sectors';

    public function findBySlug($slug)
    {
        $where = $this->getAdapter()->quoteInto('slug = ?', $slug);
        return $this->fetchAll($where, 'slug');
    }

}

