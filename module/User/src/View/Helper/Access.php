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

namespace User\View\Helper;

use Exception;
use User\Service\RbacManager;
use Laminas\View\Helper\AbstractHelper;

/**
 * This view helper is used to check user permissions
 * @package User\View\Helper
 */
class Access extends AbstractHelper
{
    /** @var RbacManager|null $rbacManager */
    private ?RbacManager $rbacManager;

    /**
     * Access constructor
     * @param RbacManager $rbacManager
     */
    public function __construct(RbacManager $rbacManager)
    {
        $this->rbacManager = $rbacManager;
    }

    /**
     * @param string $permission
     * @param array $params
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
