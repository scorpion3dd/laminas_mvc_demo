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

namespace User\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Service\RbacManager;
use User\View\Helper\Access;

/**
 * This is the factory for Access view helper. Its purpose is to instantiate the helper
 * and inject dependencies into its constructor
 * @package User\View\Helper\Factory
 */
class AccessFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return Access
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Access
    {
        $rbacManager = $container->get(RbacManager::class);

        return new Access($rbacManager);
    }
}
