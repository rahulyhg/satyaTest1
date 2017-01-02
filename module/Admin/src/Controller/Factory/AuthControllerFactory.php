<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Admin\Controller\AuthController;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Service\AuthManager;
use Admin\Service\AdminManager;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authManager = $container->get(AuthManager::class);
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);
        $userManager = $container->get(AdminManager::class);
        
        return new AuthController($entityManager, $authManager, $authService, $userManager);
    }
}
