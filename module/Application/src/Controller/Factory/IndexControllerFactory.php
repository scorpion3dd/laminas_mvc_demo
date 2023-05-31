<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Laminas Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2021-2022 scorpion3dd
 */

declare(strict_types=1);

namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\UserManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Controller\IndexController;

/**
 * Class IndexControllerFactory
 * This is the factory for IndexController
 * @package Application\Controller\Factory
 */
class IndexControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return IndexController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): IndexController
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $i18nSessionContainer = $container->get('I18nSessionContainer');
        $logger = $container->get('LoggerGlobal');
        $userManager = $container->get(UserManager::class);

        return new IndexController($entityManager, $i18nSessionContainer, $logger, $userManager);
    }
}
