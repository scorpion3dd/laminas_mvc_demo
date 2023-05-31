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

namespace Application\Controller;

use Exception;
use User\Controller\Plugin\AccessPlugin;
use User\Controller\Plugin\CurrentUserPlugin;
use User\Repository\UserRepository;
use User\Service\UserManager;
use Laminas\Http\Response;
use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\Uri\Uri;
use Laminas\View\Model\ViewModel;
use User\Entity\User;
use Doctrine\ORM\EntityManager;

/**
 * Class IndexController
 * @package Application\Controller
 * @method CurrentUserPlugin currentUser()
 * @method AccessPlugin access()
 */
class IndexController extends AbstractActionController
{
    public const COUNT_PER_PAGE = 7;

    const APP_NAME = 'Simple Web Demo Free Lottery Management Application';

    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var Container $i18nSessionContainer */
    private Container $i18nSessionContainer;

    /** @var Logger $logger */
    protected Logger $logger;

    /** @var UserManager $userManager */
    protected UserManager $userManager;

    /**
     * IndexController constructor
     * @param EntityManager $entityManager
     * @param Container $i18nSessionContainer
     * @param Logger $logger
     * @param UserManager $userManager
     */
    public function __construct(
        EntityManager $entityManager,
        Container $i18nSessionContainer,
        Logger $logger,
        UserManager $userManager
    ) {
        $this->entityManager = $entityManager;
        $this->i18nSessionContainer = $i18nSessionContainer;
        $this->logger = $logger;
        $this->userManager = $userManager;
    }

    /**
     * It displays the "Home" page
     *
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $page = (int)$this->params()->fromQuery('page', 0);
        $users = $this->userManager->getUsersPaginator($page, self::COUNT_PER_PAGE);

        return new ViewModel([
            'users' => $users
        ]);
    }

    /**
     * The "view" action displays a page allowing to view user's details
     *
     * @return ViewModel|null
     */
    public function viewAction(): ?ViewModel
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id < 1) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        /** @var User|null $user */
        $user = $userRepository->find($id);
        if (empty($user)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return null;
        }

        return new ViewModel([
            'user' => $user
        ]);
    }

    /**
     * It displays the "About" page
     *
     * @return ViewModel
     */
    public function aboutAction(): ViewModel
    {
        return new ViewModel([
            'appName' => self::APP_NAME
        ]);
    }

    /**
     * It displays the "Settings" page with the info about currently logged in user
     *
     * @return ViewModel|Response|void
     */
    public function settingsAction()
    {
        $id = $this->params()->fromRoute('id');
        if ($id != null) {
            /** @var User|null $user */
            $user = $this->entityManager->getRepository(User::class)->find($id);
        } else {
            $user = $this->currentUser();
        }
        if (empty($user)) {
            /** @var Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(404);

            return;
        }
        /** @phpstan-ignore-next-line */
        if (! $this->access('profile.any.view') && ! $this->access('profile.own.view', ['user' => $user])) {
            return $this->redirect()->toRoute('not-authorized');
        }

        return new ViewModel([
            'user' => $user
        ]);
    }

    /**
     * This action allows to change the current language
     *
     * @return Response
     * @throws Exception
     */
    public function languageAction(): Response
    {
        $languageId = $this->params()->fromRoute('id', 'en_US');
        /** @phpstan-ignore-next-line */
        $this->i18nSessionContainer->languageId = $languageId;
        $redirectUrl = $_SERVER['HTTP_REFERER'];
        if (! empty($redirectUrl)) {
            // The below check is to prevent possible redirect attack
            // (if someone tries to redirect user to another domain)
            $url = new Uri($redirectUrl);
            if ($url->isValid() && $url->getHost() != null) {
                $redirectUrl = $url->getPath();
            } else {
                $message = 'Incorrect redirect URL: ' . $redirectUrl;
                $this->logger->log(Logger::ERR, $message);
                throw new Exception($message);
            }
        }
        if (empty($redirectUrl)) {
            return $this->redirect()->toRoute('home');
        } else {
            return $this->redirect()->toUrl($redirectUrl);
        }
    }
}
