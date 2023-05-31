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

namespace User\Controller;

use Exception;
use User\Service\AuthManager;
use User\Service\UserManager;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\Result;
use Laminas\Uri\Uri;
use User\Form\LoginForm;

/**
 * This controller is responsible for letting the user to log in and log out
 * Class AuthController
 * @package User\Controller
 */
class AuthController extends AbstractActionController
{
    /** @var AuthManager $authManager */
    private AuthManager $authManager;

    /** @var UserManager $userManager */
    private UserManager $userManager;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * AuthController constructor
     * @param AuthManager $authManager
     * @param UserManager $userManager
     * @param Logger $logger
     */
    public function __construct(
        AuthManager $authManager,
        UserManager $userManager,
        Logger $logger
    ) {
        $this->authManager = $authManager;
        $this->userManager = $userManager;
        $this->logger = $logger;
    }

    /**
     * Authenticates user given email address and password credentials
     *
     * @return ViewModel|Response
     * @throws Exception
     */
    public function loginAction(): ViewModel|Response
    {
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl) > 2048) {
            $message = 'Too long redirectUrl argument passed';
            $this->logger->log(Logger::ERR, $message);
            throw new Exception($message);
        }
        $this->userManager->createAdminUserIfNotExists();
        $form = new LoginForm();
        $form->get('redirect_url')->setValue($redirectUrl);
        $isLoginError = false;
        /** @var Request $request */
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                /** @var array $data */
                $data = $form->getData();
                $result = $this->authManager->login(
                    $data['email'],
                    $data['password'],
                    (int)$data['remember_me']
                );
                if ($result->getCode() == Result::SUCCESS) {
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    if (! empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack
                        // (if someone tries to redirect user to another domain)
                        $uri = new Uri($redirectUrl);
                        if (! $uri->isValid() || $uri->getHost() != null) {
                            $message = 'Incorrect redirect URL: ' . $redirectUrl;
                            $this->logger->log(Logger::ERR, $message);
                            throw new Exception($message);
                        }
                    }
                    if (empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    $isLoginError = true;
                }
            } else {
                $isLoginError = true;
                $message = $form->getMessages();
                $this->logger->log(Logger::ERR, $message);
            }
        }

        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
        ]);
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function logoutAction(): Response
    {
        $this->authManager->logout();

        return $this->redirect()->toRoute('login');
    }

    /**
     * Displays the "Not Authorized" page
     *
     * @return ViewModel
     */
    public function notAuthorizedAction(): ViewModel
    {
        /** @var Response $response */
        $response = $this->getResponse();
        $response->setStatusCode(404);

        return new ViewModel();
    }
}
