<?php
namespace UsersACL;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\Adapter\DbTable as DbAuthAdapter;
use Zend\Session\Container;
use UsersACL\Model\UserRole;
use UsersACL\Model\PermissionTable;
use UsersACL\Model\ResourceTable;
use UsersACL\Model\RolePermissionTable;
use Zend\Authentication\AuthenticationService;
use UsersACL\Model\Role;
use UsersACL\Utility\Acl;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements \Zend\ModuleManager\Feature\ConfigProviderInterface
{

    /**
     * Bootstrap function
     *
     * @param MvcEvent $e            
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'boforeDispatch'
        ), 100);
    }

    /**
     * Before Dispatch Function
     *
     * @param MvcEvent $event            
     */
    function boforeDispatch(MvcEvent $event)
    {
        $sm = $event->getApplication()->getServiceManager();
       
        $config = $sm->get('Config');
         //\Doctrine\Common\Util\Debug::dump($config);exit;
        $list = $config['whitelist'];
        $name = $sm->get('request')
            ->getUri()
            ->getPath();
        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');
        $session = new Container('Zend_Auth');
        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');
        $session->offsetUnset('userId');
        //$session->offsetSet('userId', '1');
        //\Doctrine\Common\Util\Debug::dump($action);exit;
        //\Doctrine\Common\Util\Debug::dump($session->offsetExists('session'));exit;
        if (! ((strpos($name, 'reset-password') || in_array($name, $list))) && $session->offsetExists('userId')) {
            //\Doctrine\Common\Util\Debug::dump($action);exit;
            $serviceManager = $event->getApplication()->getServiceManager();
            
            $roleTable = $serviceManager->get('RoleTable');
//            foreach ($roleTable->getUserRoles() as $val){
//            \Doctrine\Common\Util\Debug::dump($val);
//            }
            //exit;
            
            $userRoleTable = $serviceManager->get('UserRoleTable');
            $roleID = $userRoleTable->getUserRoles('user_id = ' . $session->offsetGet('userId'), array(
                'role_id'
            ));
            $roleName = $roleTable->getUserRoles('rid = ' . $roleID[0]['role_id'], array(
                'role_name'
            ));
            $userRole = $roleName[0]['role_name'];
            
            $acl = $serviceManager->get('Acl');
            $acl->initAcl();
            \Doctrine\Common\Util\Debug::dump($controller);
            $status = $acl->isAccessAllowed($userRole, $controller, $action);
            if (! $status) {
                die('Permission denied');
            }
        }
    }

    /**
     * Function GetConfig
     * Get the Config details
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    /**
     * Function Get Service Config
     *
     * @return multitype:multitype:NULL |\Zend\Authentication\AuthenticationService|\UsersACL\Utility\Acl|\UsersACL\Model\Role|\UsersACL\Model\UserRole|\UsersACL\Model\PermissionTable|\UsersACL\Model\ResourceTable|\UsersACL\Model\RolePermissionTable
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                
                'AuthService' => function ($serviceManager)
                {
                    $adapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $dbAuthAdapter = new DbAuthAdapter($adapter, 'users', 'email', 'password');
                    $auth = new AuthenticationService();
                    $auth->setAdapter($dbAuthAdapter);
                    return $auth;
                },
                'Acl' => function ($serviceManager)
                {
                    return new Acl(
                            $serviceManager->get('RoleTable'),
                            $serviceManager->get('UserRoleTable'),
                            $serviceManager->get('PermissionTable'),
                            $serviceManager->get('ResourceTable'),
                            $serviceManager->get('RolePermissionTable')
                            );
                },
                'RoleTable' => function ($serviceManager)
                {
                    return new Role($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                Model\Role::class => function($container) {
                    $tableGateway = $container->get(Model\RoleTableGateway::class);
                    return new Model\Role($tableGateway);
                },
                Model\RoleTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    //$dbAdapter = $container->get('Zend\Db\Adapter\Adapter');
                    //$resultSetPrototype = new ResultSet();
                    //$resultSetPrototype->setArrayObjectPrototype(new Model\RoleTable());
                    return new TableGateway('role', $dbAdapter, null, null);
                },        
                'UserRoleTable' => function ($serviceManager)
                {
                    return new UserRole($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'PermissionTable' => function ($serviceManager)
                {
                    return new PermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'ResourceTable' => function ($serviceManager)
                {
                    return new ResourceTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'RolePermissionTable' => function ($serviceManager)
                {
                    return new RolePermissionTable($serviceManager->get('Zend\Db\Adapter\Adapter'));
                }
            )
        );
    }
}