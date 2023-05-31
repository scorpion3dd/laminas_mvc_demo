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

namespace User\Controller\Plugin;

use User\Service\RbacManager;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Exception;

/**
 * Class AccessPlugin
 * This controller plugin is used for role-based access control (RBAC)
 * @package User\Controller\Plugin
 */
class AccessPlugin extends AbstractPlugin
{
    /** @var RbacManager $rbacManager */
    private RbacManager $rbacManager;

    /**
     * AccessPlugin constructor
     * @param RbacManager $rbacManager
     */
    public function __construct(RbacManager $rbacManager)
    {
        $this->rbacManager = $rbacManager;
    }

    /**
     * Checks whether the currently logged in user has the given permission
     * @param string $permission - permission name
     * @param array $params - optional params (used only if an assertion is associated with permission)
     *
     * @return bool
     * @throws Exception
     */
    public function __invoke(string $permission, array $params = []): bool
    {
        return $this->rbacManager->isGranted(null, $permission, $params);
    }

    /**
     * @param RbacManager|null $rbacManager
     */
    public function setRbacManager(?RbacManager $rbacManager): void
    {
        $this->rbacManager = $rbacManager;
    }
}
