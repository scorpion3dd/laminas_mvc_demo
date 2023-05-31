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

namespace User;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Events;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Listeners\MysqlSessionInit;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Log\Logger;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Controller\AbstractActionController;
use User\Controller\AuthController;
use User\Service\AuthManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\SessionManager;
use Laminas\Uri\Http as HttpUri;

/**
 * Class Module
 * @package User
 */
class Module
{
    /**
     * This method returns the path to module.config.php file
     *
     * @return array
     */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows to register event listeners
     * @param MvcEvent $event
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onBootstrap(MvcEvent $event): void
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $event->getApplication()->getServiceManager();
        /** @var Logger $logger */
        $logger = $serviceManager->get('LoggerGlobal');
        $eventManager = $event->getApplication()->getEventManager();
        $sharedEventManager = $eventManager->getSharedManager();
        $sharedEventManager->attach(
            AbstractActionController::class,
            MvcEvent::EVENT_DISPATCH,
            [$this, 'onDispatch'],
            100
        );
        /** @var SessionManager $sessionManager */
        $sessionManager = $serviceManager->get('Laminas\Session\SessionManager');
        $this->forgetInvalidSession($sessionManager);

        /** @var EventManager $doctrineEventManager */
        $doctrineEventManager = $serviceManager->get('doctrine.eventmanager.orm_default');
        $doctrineEventManager->addEventListener(Events::postConnect, new MysqlSessionInit(
            $logger,
            $serviceManager,
            'orm_default'
        ));
    }

    /**
     * @param SessionManager $sessionManager
     *
     * @return void
     */
    protected function forgetInvalidSession(SessionManager $sessionManager): void
    {
        try {
            $sessionManager->start();
            return;
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
        }
        session_unset();
        // @codeCoverageIgnoreEnd
    }

    /**
     * Event listener method for the 'Dispatch' event. We listen to the Dispatch
     * event to call the access filter. The access filter allows to determine if
     * the current visitor is allowed to see the page or not. If he/she
     * is not authorized and is not allowed to see the page, we redirect the user
     * to the login page
     * @param MvcEvent $event
     *
     * @return Response|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onDispatch(MvcEvent $event): ?Response
    {
        /** @var Logger $logger */
        $logger = $event->getApplication()->getServiceManager()->get('LoggerGlobal');
        /** @var AbstractActionController $controller */
        $controller = $event->getTarget();
        $controllerName = $event->getRouteMatch()->getParam('controller', null);
        $actionName = $event->getRouteMatch()->getParam('action', null);
        // Convert dash-style action name to camel-case
        $actionName = str_replace('-', '', lcfirst(ucwords($actionName, '-')));
        $message = "controllerName: " . $controllerName . ", actionName: " .$actionName;
        $logger->log(Logger::DEBUG, $message);
        /** @var AuthManager $authManager */
        $authManager = $event->getApplication()->getServiceManager()->get(AuthManager::class);
        // Execute the access filter on every controller except AuthController (to avoid infinite redirect)
        if ($controllerName != AuthController::class) {
            $result = $authManager->filterAccess($controllerName, $actionName);
            if ($result == AuthManager::AUTH_REQUIRED) {
                // Remember the URL of the page the user tried to access
                // We will redirect the user to that URL after successful login
                /** @var Request $request */
                $request = $event->getApplication()->getRequest();
                /** @var HttpUri $uri */
                $uri = $request->getUri();
                // Make the URL relative (remove scheme, user info, host name and port)
                // to avoid redirecting to other domain by a malicious user
                $uri->setScheme(null)
                    ->setHost(null)
                    ->setPort(null)
                    ->setUserInfo(null);
                $redirectUrl = $uri->toString();
                $logger->log(Logger::DEBUG, $redirectUrl);

                return $controller->redirect()->toRoute(
                    'login',
                    [],
                    ['query' => ['redirectUrl' => $redirectUrl]]
                );
            } elseif ($result == AuthManager::ACCESS_DENIED) {
                $logger->log(Logger::DEBUG, 'AuthManager::ACCESS_DENIED');

                return $controller->redirect()->toRoute('not-authorized');
            }
        }

        return null;
    }
}
