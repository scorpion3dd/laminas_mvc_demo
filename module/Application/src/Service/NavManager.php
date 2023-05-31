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

namespace Application\Service;

use Exception;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\RbacManager;
use Laminas\Authentication\AuthenticationService;
use Laminas\Log\Logger;
use Laminas\View\Helper\Url;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not
 * Class NavManager
 * @package Application\Service
 */
class NavManager extends AbstractService
{
    public const PERMISSION_USER_MANAGE = 'user.manage';
    public const PERMISSION_MANAGE = 'permission.manage';
    public const PERMISSION_ROLE_MANAGE = 'role.manage';

    /** @var AuthenticationService $authService */
    private AuthenticationService $authService;

    /** @var Url $urlHelper */
    private Url $urlHelper;

    /** @var RbacManager $rbacManager */
    private RbacManager $rbacManager;

    /**
     * NavManager constructor
     * @param ContainerInterface $container
     * @param AuthenticationService $authService
     * @param Url $urlHelper
     * @param RbacManager $rbacManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container, AuthenticationService $authService, Url $urlHelper, RbacManager $rbacManager)
    {
        parent::__construct($container);
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        $this->rbacManager = $rbacManager;
    }

    /**
     * This method returns menu items depending on whether user has logged in or not
     *
     * @return array
     * @throws Exception
     */
    public function getMenuItems(): array
    {
        $url = $this->urlHelper;
        $items = [];
        $items[] = [
            'id' => 'home',
            'label' => $this->translator->translate('Home'),
            'link'  => $url('home')
        ];
        $items[] = [
            'id' => 'about',
            'label' => $this->translator->translate('About'),
            'link'  => $url('about')
        ];
        $items[] = [
            'id' => 'language',
            'label' => $this->translator->translate('Language'),
            'icon' => 'glyphicon-globe',
            'float' => 'right',
            'dropdown' => [
                [
                    'id' => 'en',
                    'label' => $this->translator->translate('English (EN)'),
                    'link' => $url('application', ['action' => 'language', 'id' => 'en_US'])
                ],
                [
                    'id' => 'ru',
                    'label' => $this->translator->translate('Russian (RU)'),
                    'link' => $url('application', ['action' => 'language', 'id' => 'ru_RU'])
                ],
                [
                    'id' => 'es',
                    'label' => $this->translator->translate('Spanish (ES)'),
                    'link' => $url('application', ['action' => 'language', 'id' => 'es_ES'])
                ]
            ]
        ];
        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users
        if (! $this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'login',
                'label' => $this->translator->translate('Sign in'),
                'link'  => $url('login'),
                'float' => 'right'
            ];
        } else {
            // Determine which items must be displayed in Admin dropdown
            $adminDropdownItems = [];
            try {
                if ($this->rbacManager->isGranted(null, self::PERMISSION_USER_MANAGE)) {
                    $adminDropdownItems[] = [
                        'id' => 'users',
                        'label' => $this->translator->translate('Manage Users'),
                        'link' => $url('users')
                    ];
                }
            } catch (Exception $e) {
                $message = 'Error: Message - ' . $e->getMessage()
                    . ', in file - ' . $e->getFile()
                    . ', in line - ' . $e->getLine();
                $this->logger->log(Logger::ERR, $message);
            }
            try {
                if ($this->rbacManager->isGranted(null, self::PERMISSION_MANAGE)) {
                    $adminDropdownItems[] = [
                        'id' => 'permissions',
                        'label' => $this->translator->translate('Manage Permissions'),
                        'link' => $url('permissions')
                    ];
                }
            } catch (Exception $e) {
                $message = 'Error: Message - ' . $e->getMessage()
                    . ', in file - ' . $e->getFile()
                    . ', in line - ' . $e->getLine();
                $this->logger->log(Logger::ERR, $message);
            }
            try {
                if ($this->rbacManager->isGranted(null, self::PERMISSION_ROLE_MANAGE)) {
                    $adminDropdownItems[] = [
                        'id' => 'roles',
                        'label' => $this->translator->translate('Manage Roles'),
                        'link' => $url('roles')
                    ];
                }
            } catch (Exception $e) {
                $message = 'Error: Message - ' . $e->getMessage()
                    . ', in file - ' . $e->getFile()
                    . ', in line - ' . $e->getLine();
                $this->logger->log(Logger::ERR, $message);
            }
            try {
                if ($this->rbacManager->isGranted(null, self::PERMISSION_USER_MANAGE)) {
                    $adminDropdownItems[] = [
                        'id' => 'consumer',
                        'label' => $this->translator->translate('Consumer'),
                        'link'  => $url('consumer')
                    ];
                    $adminDropdownItems[] = [
                        'id' => 'logs',
                        'label' => $this->translator->translate('Logs'),
                        'link'  => $url('logs')
                    ];
                }
            } catch (Exception $e) {
                $message = 'Error: Message - ' . $e->getMessage()
                    . ', in file - ' . $e->getFile()
                    . ', in line - ' . $e->getLine();
                $this->logger->log(Logger::ERR, $message);
            }
            if (count($adminDropdownItems) != 0) {
                $items[] = [
                    'id' => 'admin',
                    'label' => $this->translator->translate('Admin'),
                    'dropdown' => $adminDropdownItems
                ];
            }
            $items[] = [
                'id' => 'logout',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'settings',
                        'label' => $this->translator->translate('Settings'),
                        'link' => $url('application', ['action' => 'settings'])
                    ],
                    [
                        'id' => 'logout',
                        'label' => $this->translator->translate('Sign out'),
                        'link' => $url('logout')
                    ],
                ]
            ];
        }

        return $items;
    }

    /**
     * @param RbacManager $rbacManager
     *
     * @return $this
     */
    public function setRbacManager(RbacManager $rbacManager): self
    {
        $this->rbacManager = $rbacManager;

        return $this;
    }
}
