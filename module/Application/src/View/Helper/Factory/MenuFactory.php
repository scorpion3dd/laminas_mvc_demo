<?php
namespace Application\View\Helper\Factory;

use Exception;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\View\Helper\Menu;
use Application\Service\NavManager;

/**
 * This is the factory for Menu view helper. Its purpose is to instantiate the
 * helper and init menu items.
 */
class MenuFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return Menu
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Menu
    {
        $translator = $container->get('MvcTranslator');
        /** @var NavManager $navManager */
        $navManager = $container->get(NavManager::class);
        $items = $navManager->getMenuItems();

        return new Menu($translator, $items);
    }
}
