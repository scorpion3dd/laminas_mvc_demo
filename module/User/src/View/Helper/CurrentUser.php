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

use Doctrine\ORM\EntityManager;
use Exception;
use Laminas\Authentication\AuthenticationService;
use Laminas\Log\Logger;
use Laminas\View\Helper\AbstractHelper;
use User\Entity\User;

/**
 * This view helper is used for retrieving the User entity of currently logged in user
 * @package User\View\Helper
 */
class CurrentUser extends AbstractHelper
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var AuthenticationService $authService */
    private AuthenticationService $authService;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * @var User|null
     */
    private ?User $user;

    /**
     * CurrentUser constructor
     * @param EntityManager $entityManager
     * @param AuthenticationService $authService
     * @param Logger $logger
     */
    public function __construct(EntityManager $entityManager, AuthenticationService $authService, Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->logger = $logger;
    }

    /**
     * Returns the current User or null if not logged in
     * @param bool $useCachedUser - If true, the User entity is fetched only on the first call (and cached on subsequent calls)
     *
     * @return User|null
     * @throws Exception
     */
    public function __invoke(bool $useCachedUser = true): ?User
    {
        if ($useCachedUser && $this->user !== null) {
            return $this->user;
        }
        if ($this->authService->hasIdentity()) {
            /** @var User $user */
            $user = $this->entityManager->getRepository(User::class)->findOneBy([
                'email' => $this->authService->getIdentity()
            ]);
            $this->user = $user;
            if ($this->user == null) {
                $message = 'Not found user with such ID';
                $this->logger->log(Logger::ERR, $message);
                throw new Exception($message);
            }

            return $this->user;
        }

        return null;
    }

    /**
     * @param AuthenticationService $authService
     */
    public function setAuthService(AuthenticationService $authService): void
    {
        $this->authService = $authService;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
