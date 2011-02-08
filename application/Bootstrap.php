<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
	$view = $this->getResource('view');
        $view->doctype('HTML5');
        
        return $view;
    }

    protected function _initLocale()
    {
        $locale = new Zend_Locale('en_AU');
        Zend_Registry::set('Zend_Locale', $locale);
    }

    protected function _initAppAutoload()
    {
        $moduleLoad = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH
        ));
    }

    protected function _initDoctrine()
    {
        $this->getApplication()->getAutoloader()
            ->pushAutoloader(array('Doctrine', 'autoload'));
        spl_autoload_register(array('Doctrine', 'modelsAutoload'));

        $doctrineConfig = $this->getOption('doctrine');
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(
          Doctrine::ATTR_MODEL_LOADING,
          $doctrineConfig['model_autoloading']
        );

        Doctrine::loadModels($doctrineConfig['models_path']);

        $conn = Doctrine_Manager::connection($doctrineConfig['dsn'],'doctrine');
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        return $conn;
    }

    protected function _initControllerPlugins()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new My_Controller_Plugin_UserLoginControl());
        $frontController->registerPlugin(new My_Controller_Plugin_AdminLoginControl());
    }
}

