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

    protected function _initMail()
    {
        Zend_Mail::setDefaultFrom('xlr8ufitness@live.com.au', 'XLR8U Fitness');
        Zend_Mail::setDefaultReplyTo('xlr8ufitness@live.com.au', 'XLR8U Fitness');
        $transport = new Zend_Mail_Transport_Sendmail();
        Zend_Mail::setDefaultTransport($transport);
    }

    /**
    * used for handling top-level navigation
    * @return Zend_Navigation
    */
    protected function _initNavigation()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');

        $container = new Zend_Navigation($config);

        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl(new My_Acl());
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole(My_Acl_Roles::GUEST);

        $view->navigation($container);
    }
}

