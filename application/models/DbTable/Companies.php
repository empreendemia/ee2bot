<?php

class EeBot_Model_DbTable_Companies extends Zend_Db_Table_Abstract
{

    protected $_name = 'companies';
    
    protected $_referenceMap    = array(
        'Sector' => array(
            'columns'           => 'sector_id',
            'refTableClass'     => 'Ee_Model_DbTable_Sector',
            'refColumns'        => 'id'
        )
    );

    public function findBySlug($slug)
    {
        $where = $this->getAdapter()->quoteInto('slug = ?', $slug);
        return $this->fetchAll($where, 'slug');
    }

}

