<?php

/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use User\Controller\AuthController;
use User\Service\AuthManager;

class Module {

    protected $whitelist = array(
            //'Admin\Controller\Index',
//        'Application\Controller\Index',
//        'Application\Controller\Pages',
//        'Application\Controller\User',
//        'Application\Controller\App',
//        'Application\Controller\Account',
//        'Application\Controller\Profile',
//        'Application\Controller\Membership',
//        'Application\Controller\Matrimonial',
//        'Application\Controller\Community',
//        'Application\Controller\News',
//        'Application\Controller\Events',
//        'Application\Controller\Posts',
//        'Application\Controller\Obituary',
//        'Application\Controller\Justborn',
//        'Common\Controller\Common',
    );

    /**
     * This method returns the path to module.config.php file.
     */
    public function getConfig() {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners. 
     */
    public function onBootstrap(MvcEvent $event) {
        // Get event manager.
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        // Register the event listener method. 
        $sharedEventManager->attach(AbstractActionController::class, MvcEvent::EVENT_DISPATCH, [$this, 'onDispatch'], 100);
    }

    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authorized and is not allowed to see the page, we redirect the user 
     * to the login page.
     */
    public function onDispatch(MvcEvent $event) {
        // Get controller and action to which the HTTP request was dispatched.
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);

        // Convert dash-style action name to camel-case.
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));

        // Get the instance of AuthManager service.
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);

        // Execute the access filter on every controller except AuthController
        // (to avoid infinite redirect).
        $match = $event->getRouteMatch();
        //\Doctrine\Common\Util\Debug::dump($match->getMatchedRouteName());
        //\Doctrine\Common\Util\Debug::dump(0 === strpos($match->getMatchedRouteName(), 'zfcadmin'));exit;
//        if (0 === strpos($match->getMatchedRouteName(), 'zfcadmin')) {
//            \Doctrine\Common\Util\Debug::dump($match instanceof RouteMatch);exit;
//            //\Doctrine\Common\Util\Debug::dump($authManager->filterAccess($controllerName, $actionName));exit;
//            if ($controllerName != AuthController::class &&
//                    !$authManager->filterAccess($controllerName, $actionName)) {
//
//                // Remember the URL of the page the user tried to access. We will
//                // redirect the user to that URL after successful login.
//                $uri = $event->getApplication()->getRequest()->getUri();
//                // Make the URL relative (remove scheme, user info, host name and port)
//                // to avoid redirecting to other domain by a malicious user.
//                $uri->setScheme(null)
//                        ->setHost(null)
//                        ->setPort(null)
//                        ->setUserInfo(null);
//                $redirectUrl = $uri->toString();
//                //\Doctrine\Common\Util\Debug::dump($redirectUrl);
//                //exit;
//                //exit;
//                // Redirect the user to the "Login" page.
//                return $controller->redirect()->toRoute('zfcadmin/login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
//            }
//        }
//        
//        if (0 === strpos($match->getMatchedRouteName(), 'lowyer')) {
//            
//            //return $controller->redirect()->toRoute('lowyer/login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
//            //\Doctrine\Common\Util\Debug::dump($authManager->filterAccess($controllerName, $actionName));exit;
//            //\Doctrine\Common\Util\Debug::dump($match->getMatchedRouteName());exit;
//            if ($controllerName != AuthController::class &&
//                    !$authManager->filterAccess($controllerName, $actionName)) {
//
//                // Remember the URL of the page the user tried to access. We will
//                // redirect the user to that URL after successful login.
//                $uri = $event->getApplication()->getRequest()->getUri();
//                // Make the URL relative (remove scheme, user info, host name and port)
//                // to avoid redirecting to other domain by a malicious user.
//                $uri->setScheme(null)
//                        ->setHost(null)
//                        ->setPort(null)
//                        ->setUserInfo(null);
//                $redirectUrl = $uri->toString();
//                //\Doctrine\Common\Util\Debug::dump($redirectUrl);exit;
//                //exit;
//                //exit;
//                // Redirect the user to the "Login" page.
//                return $controller->redirect()->toRoute('lowyer/login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
//            }
//        }
       // if (0 === strpos($match->getMatchedRouteName(), 'users')) {
            
            //return $controller->redirect()->toRoute('lowyer/login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
            //\Doctrine\Common\Util\Debug::dump($authManager->filterAccess($controllerName, $actionName));exit;
            //\Doctrine\Common\Util\Debug::dump($match->getMatchedRouteName());exit;
            if ($controllerName != AuthController::class &&
                    !$authManager->filterAccess($controllerName, $actionName)) {

                // Remember the URL of the page the user tried to access. We will
                // redirect the user to that URL after successful login.
                $uri = $event->getApplication()->getRequest()->getUri();
                // Make the URL relative (remove scheme, user info, host name and port)
                // to avoid redirecting to other domain by a malicious user.
                $uri->setScheme(null)
                        ->setHost(null)
                        ->setPort(null)
                        ->setUserInfo(null);
                $redirectUrl = $uri->toString();
                //\Doctrine\Common\Util\Debug::dump($redirectUrl);exit;
                //exit;
                //exit;
                // Redirect the user to the "Login" page.
                if (0 === strpos($match->getMatchedRouteName(), 'lowyer')) {
                    return $controller->redirect()->toRoute('lowyer/login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
                }
                if (0 === strpos($match->getMatchedRouteName(), 'zfcadmin')) {
                    return $controller->redirect()->toRoute('zfcadmin/login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
                }
                return $controller->redirect()->toRoute('login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
            }
        //}
        //\Doctrine\Common\Util\Debug::dump($event->getApplication()->getServiceManager()->get);

//        if ($controllerName != AuthController::class &&
//                !$authManager->filterAccess($controllerName, $actionName)) {
//
//            // Remember the URL of the page the user tried to access. We will
//            // redirect the user to that URL after successful login.
//            $uri = $event->getApplication()->getRequest()->getUri();
//            // Make the URL relative (remove scheme, user info, host name and port)
//            // to avoid redirecting to other domain by a malicious user.
//            $uri->setScheme(null)
//                    ->setHost(null)
//                    ->setPort(null)
//                    ->setUserInfo(null);
//            $redirectUrl = $uri->toString();
//            //\Doctrine\Common\Util\Debug::dump($redirectUrl);
//            //exit;
//            //exit;
//            // Redirect the user to the "Login" page.
//            return $controller->redirect()->toRoute('login', [], ['query' => ['redirectUrl' => $redirectUrl]]);
//        }
    }

}
