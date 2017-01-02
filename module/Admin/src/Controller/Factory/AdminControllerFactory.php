<?php
namespace Admin\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Admin\Controller\AdminController;
use Admin\Service\AdminManager;

/**
 * This is the factory for AdminController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class AdminControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get(AdminManager::class);
        
        // Instantiate the controller and inject dependencies
        return new AdminController($entityManager, $userManager);
    }
}