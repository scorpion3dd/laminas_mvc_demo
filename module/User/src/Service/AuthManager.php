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

use Exception;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;
use Laminas\Log\Logger;
use Laminas\Session\SessionManager;

/**
 * The AuthManager service is responsible for user's login/logout and simple access
 * filtering. The access filtering feature checks whether the current visitor
 * is allowed to see the given page or not
 * @package User\Service
 */
class AuthManager
{
    protected const MODE_RESTRICTIVE = 'restrictive';
    public const MODE_PERMISSIVE = 'permissive';

    public const ALLOW_STAR = '*';
    public const ALLOW_AT = '@';
    public const ALLOW_PLUS = '+';

    public const ACCESS_GRANTED = 1;
    public const AUTH_REQUIRED  = 2;
    public const ACCESS_DENIED  = 3;

    /** @var AuthenticationService $authService */
    private AuthenticationService $authService;

    /** @var SessionManager $sessionManager */
    private SessionManager $sessionManager;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * Contents of the 'access_filter' config key
     * @var array $config
     */
    private array $config;

    /** @var RbacManager $rbacManager */
    private RbacManager $rbacManager;

    /**
     * AuthManager constructor
     * @param AuthenticationService $authService
     * @param SessionManager $sessionManager
     * @param array $config
     * @param RbacManager $rbacManager
     * @param Logger $logger
     */
    public function __construct(
        AuthenticationService $authService,
        SessionManager $sessionManager,
        array $config,
        RbacManager $rbacManager,
        Logger $logger
    ) {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
        $this->rbacManager = $rbacManager;
        $this->logger = $logger;
    }

    /**
     * Performs a login attempt. If $rememberMe argument is true, it forces the session
     * to last for one month (otherwise the session expires on one hour)
     * @param string $email
     * @param string $password
     * @param int $rememberMe
     *
     * @return Result
     * @throws Exception
     */
    public function login(string $email, string $password, int $rememberMe): Result
    {
        // Check if user has already logged in. If so, do not allow to log in twice
        if ($this->authService->getIdentity() != null) {
            $message = 'Already logged in';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        // Authenticate with login/password
        /** @var AuthAdapter $authAdapter */
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();
        if ($result->getCode() == Result::SUCCESS && $rememberMe) {
            // Session cookie will expire in 1 month (30 days)
            $this->sessionManager->rememberMe($this->config['gc_maxlifetime']);
        }

        return $result;
    }

    /**
     * Performs user logout
     *
     * @return void
     * @throws Exception
     */
    public function logout(): void
    {
        // Allow to log out only when user is logged in
        if ($this->authService->getIdentity() == null) {
            $message = 'The user is not logged in';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        // Remove identity from session
        $this->authService->clearIdentity();
    }

    /**
     * This is a simple access control filter. It is able to restrict unauthorized
     * users to visit certain pages.
     * This method uses the 'access_filter' key in the config file and determines
     * whenther the current visitor is allowed to access the given controller action
     * or not. It returns true if allowed; otherwise false.
     *
     * @param string $controllerName
     * @param string $actionName
     *
     * @return int
     * @throws Exception
     */
    public function filterAccess(string $controllerName, string $actionName): int
    {
        // Determine mode - 'restrictive' (default) or 'permissive'. In restrictive
        // mode all controller actions must be explicitly listed under the 'access_filter'
        // config key, and access is denied to any not listed action for unauthorized users.
        // In permissive mode, if an action is not listed under the 'access_filter' key,
        // access to it is permitted to anyone (even for not logged in users.
        // Restrictive mode is more secure and recommended to use.
        $mode = isset($this->config['options']['mode']) ? $this->config['options']['mode'] : self::MODE_RESTRICTIVE;
        if ($mode != self::MODE_RESTRICTIVE && $mode != self::MODE_PERMISSIVE) {
            $message = 'Invalid access filter mode (expected either restrictive or permissive mode';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        if (isset($this->config['controllers'][$controllerName])) {
            $items = $this->config['controllers'][$controllerName];
            foreach ($items as $item) {
                $actionList = $item['actions'];
                $allow = $item['allow'];
                if (is_array($actionList) && in_array($actionName, $actionList) || $actionList == self::ALLOW_STAR) {
                    if ($allow == self::ALLOW_STAR) {
                        // Anyone is allowed to see the page
                        return self::ACCESS_GRANTED;
                    } elseif (! $this->authService->hasIdentity()) {
                        // Only authenticated user is allowed to see the page
                        return self::AUTH_REQUIRED;
                    }
                    if ($allow == self::ALLOW_AT) {
                        // Any authenticated user is allowed to see the page
                        return self::ACCESS_GRANTED;
                    } elseif (substr($allow, 0, 1) == self::ALLOW_AT) {
                        // Only the user with specific identity is allowed to see the page
                        $identity = substr($allow, 1);
                        if ($this->authService->getIdentity() == $identity) {
                            return self::ACCESS_GRANTED;
                        } else {
                            return self::ACCESS_DENIED;
                        }
                    } elseif (substr($allow, 0, 1) == self::ALLOW_PLUS) {
                        // Only the user with this permission is allowed to see the page
                        $permission = substr($allow, 1);
                        if ($this->rbacManager->isGranted(null, $permission)) {
                            return self::ACCESS_GRANTED;
                        } else {
                            return self::ACCESS_DENIED;
                        }
                    } else {
                        $message = 'Unexpected value for "allow" - expected ' .
                            'either "?", "@", "@identity" or "+permission"';
                        $this->logger->log(Logger::ERR, $message);
                        throw new Exception($message);
                    }
                }
            }
        }
        // In restrictive mode, we require authentication for any action not listed under
        // 'access_filter' key and deny access to authorized users (for security reasons)
        if ($mode == self::MODE_RESTRICTIVE) {
            if (! $this->authService->hasIdentity()) {
                return self::AUTH_REQUIRED;
            } else {
                return self::ACCESS_DENIED;
            }
        }

        // Permit access to this page
        return self::ACCESS_GRANTED;
    }

    /**
     * @return AuthenticationService
     */
    public function getAuthService(): AuthenticationService
    {
        return $this->authService;
    }
}
