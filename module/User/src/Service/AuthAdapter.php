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

namespace User\Service;

use Doctrine\ORM\EntityManager;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use User\Entity\User;
use Laminas\Session\Container;

/**
 * Adapter used for authenticating user. It takes login and password on input
 * and checks the database if there is a user with such login (email) and password.
 * If such user exists, the service returns its identity (email). The identity
 * is saved to session and can be retrieved later with Identity view helper provided by ZF3.
 * @package User\Service
 */
class AuthAdapter implements AdapterInterface
{
    /** @var string $email */
    private string $email;

    /** @var string $password */
    private string $password;

    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var Container $sessionContainer */
    private Container $sessionContainer;

    /**
     * AuthAdapter constructor
     * @param EntityManager $entityManager
     * @param Container $sessionContainer
     */
    public function __construct(EntityManager $entityManager, Container $sessionContainer)
    {
        $this->entityManager = $entityManager;
        $this->sessionContainer = $sessionContainer;
    }

    /**
     * @param string $email
     *
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string $password
     *
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     */
    public function authenticate(): Result
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->email]);
        if (empty($user)) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['Invalid credentials.']
            );
        }
        // If the user with such email exists, we need to check if it is active or retired.
        // Do not allow retired users to log in.
        if ($user->getStatus() == User::STATUS_DISACTIVE_ID) {
            return new Result(
                Result::FAILURE,
                null,
                ['User is Disactived.']
            );
        }
        // Now we need to calculate hash based on user-entered password and compare
        // it with the password hash stored in database.
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();
        if ($bcrypt->verify($this->password, $passwordHash)) {
            /** @phpstan-ignore-next-line */
            $this->sessionContainer->user_id = $user->getId();

            // The password hash matches. Return user identity (email) to be saved in session for later use
            return new Result(
                Result::SUCCESS,
                $this->email,
                ['Authenticated successfully.']
            );
        }
        // If password check didn't pass return 'Invalid Credential' failure status.
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid credentials.']
        );
    }
}
