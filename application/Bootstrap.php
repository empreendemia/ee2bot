<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initAutoload() {
        
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH,
            'namespace' => 'EeBot'
        ));

        $resourceLoader->addResourceTypes(array(
           'model' => array(
               'path' => 'models/data/',
               'namespace' => 'Model_Data'
           )
        ));

        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/controllers/helpers',
            'EeBot_Controller_Helper_');

        return $resourceLoader;
    }

}

