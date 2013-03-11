<?php
/**
 * Products.php - Ee_Model_Products
 * Mapper de produtos
 * 
 * @package models
 * @author Mauro Ribeiro
 * @since 2011-09-06
 */
class EeBot_Model_Products
{

    private $_dbTable;

    /**
     * Constrói a porra toda
     */
    public function __construct() {
        $this->_dbTable = new EeBot_Model_DbTable_Products();
    }

    /**
     * Transforma um objeto em um array de dados
     * 
     * @param $object   objeto a ser transformado
     * @return array    objeto transformado em array
     * @author Mauro Ribeiro
     */
    private function _dataArray($object) {
        $data = array();
        foreach ($object as $index => $value) {
            if (is_object($value) == false && $value != null) {
                $data[$index] = $value;
            }
        }
        return $data;
    }

    /**
     * Procura por um produto
     * @param int|string $product_id        id do produto
     * @param int $company_id               id da empresa
     * @return EeBot_Model_Data_Product     produto procurado
     * @author Mauro Ribeiro
     */
    public function find($product_id, $company_id = null) {

        // procura por id
        if (is_numeric($product_id)) {
            $select = $this->_dbTable->select()
                    ->where('`id` = ?', $product_id)
                    ->order('sort');
        }
        // procura pelo slug
        else {
            $select = $this->_dbTable->select()
                    ->where('`slug` = ?', $product_id)
                    ->where('`company_id` = ?', $company_id)
                    ->order('sort');
        }
        $rows = $this->_dbTable->fetchAll($select);

        // se não achou nada
        if (0 == count($rows)) {
            return;
        }

        $product = new EeBot_Model_Data_Product();
        $product->set($rows[0]);

        // empresa do produto
        $company_mapper = new EeBot_Model_Companies();
        $product->company = $company_mapper->find($product->company_id);

        return $product;
    }
    
    /**
     * Salva os dados do produto
     * @param object $object
     * @author Mauro Ribeiro
     */
    function save($object) {
        // atualiza
        if (isset($object->id)) {
            $save = $this->_dbTable->update($this->_dataArray($object), array('id = ?' => $object->id));
        }
        // insere
        else {
            $save = $this->_dbTable->insert($this->_dataArray($object));
            $object->id = $this->_dbTable->getAdapter()->lastInsertId();
        }
        return $save;
    }

    /**
     * Procura por ofertas ativas e expiradas e salva o status como desativada
     * @return array(EeBot_Model_Data_Product)
     * @author Mauro Ribeiro
     */
    public function offersExpirations() {

        // procura as ofertas ativas e expiradas
        $select = $this->_dbTable
                ->select()
                ->where('offer_status = ?', 'active')
                ->where('offer_date_deadline < NOW()');

        $rows = $this->_dbTable->fetchAll($select);

        $products = array();
        foreach ($rows as $row) {
            $product = new EeBot_Model_Data_Product($row);
            $products[] = $product;
        }

        // atualiza como inativas
        $data = array();
        $data['offer_status'] = 'inactive';
        $this->_dbTable->update(
            $data,
            array(
                'offer_status = ?' => 'active',
                'offer_date_deadline < NOW()'
            )
        );

        return $products;
    }

    /**
     * Procura por rastros de Sr. Wilson nos produtos
     * @return array(EeBot_Model_Data_Product)
     * @author Mauro Ribeiro
     */
    public function findMrWilson() {
        $select = $this->_dbTable
            ->select()
            ->where('
                (upper(name) = (name COLLATE utf8_bin ) AND (length(name) - length(replace(name," ","")) > 1))
                OR
                (upper(description) = (description COLLATE utf8_bin ) AND length(description) > 0)
                OR
                (upper(about) = (about COLLATE utf8_bin ) AND length(about) > 0)
            ');

        $rows = $this->_dbTable->fetchAll($select);

        $products = array();

        foreach ($rows as $row) {
            $product = new EeBot_Model_Data_Product();
            $product->set($row);
            $products[] = $product;
        }

        return $products;
    }
}

