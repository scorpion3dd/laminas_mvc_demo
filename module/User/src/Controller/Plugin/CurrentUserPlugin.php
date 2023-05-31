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

use Laminas\Log\Logger;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use User\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Authentication\AuthenticationService;
use Exception;

/**
 * Class CurrentUserPlugin
 * This controller plugin is designed to let you get the currently logged in User entity inside your controller
 * @package User\Controller\Plugin
 */
class CurrentUserPlugin extends AbstractPlugin
{
    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var AuthenticationService $authService */
    private AuthenticationService $authService;

    /** @var Logger $logger */
    protected Logger $logger;

    /** @var User|null $user */
    private ?User $user = null;

    /**
     * CurrentUserPlugin constructor
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
     * This method is called when you invoke this plugin in your controller, example:
     * $user = $this->currentUser();
     * @param bool $useCachedUser - If true, the User entity is fetched only on the first call
     * and cached on subsequent calls
     *
     * @return User|null
     * @throws Exception
     */
    public function __invoke(bool $useCachedUser = true): ?User
    {
        if ($useCachedUser && ! empty($this->user)) {
            return $this->user;
        }
        if ($this->authService->hasIdentity()) {
            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $this->authService->getIdentity()]);
            $this->user = $user;
            if (empty($this->user)) {
                $message = 'Not found user with such email';
                $this->logger->log(Logger::ERR, $message);
                throw new Exception($message);
            }

            return $this->user;
        }

        return null;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @param AuthenticationService $authService
     */
    public function setAuthService(AuthenticationService $authService): void
    {
        $this->authService = $authService;
    }
}
