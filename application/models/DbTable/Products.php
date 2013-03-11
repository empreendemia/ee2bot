<?php

class EeBot_Model_DbTable_Products extends Zend_Db_Table_Abstract
{

    protected $_name = 'products';

    public function findBySlug($slug)
    {
        $where = $this->getAdapter()->quoteInto('slug = ?', $slug);
        return $this->fetchAll($where, 'slug');
    }
    
}

