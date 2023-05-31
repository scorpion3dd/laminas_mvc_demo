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

namespace Application;

use Laminas\Mvc\I18n\Translator;
use Laminas\Mvc\MvcEvent;
use Laminas\Session\SessionManager;
use Laminas\Validator\AbstractValidator;
use Locale;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class Module
 * @package Application
 */
class Module
{
    protected const VERSION = '3.0.0dev';

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete
     * @param MvcEvent $event
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function onBootstrap(MvcEvent $event): void
    {
        $application = $event->getApplication();
        $serviceManager = $application->getServiceManager();
        // The following line instantiates the SessionManager and automatically
        // makes the SessionManager the 'default' one to avoid passing the
        // session manager as a dependency to other models.
        $sessionManager = $serviceManager->get(SessionManager::class);

        $container = $serviceManager->get('I18nSessionContainer');
        $languageId = 'en_US';
        if (isset($container->languageId)) {
            // @codeCoverageIgnoreStart
            $languageId = $container->languageId;
            // @codeCoverageIgnoreEnd
        }
        Locale::setDefault($languageId);
        /** @var Translator $translator */
        $translator = $event->getApplication()->getServiceManager()->get('MvcTranslator');
        /** @phpstan-ignore-next-line */
        $translator->addTranslationFilePattern(
            'phpArray',
            './data/language',
            '%s.php',
            'default'
        );
        AbstractValidator::setDefaultTranslator(new \Laminas\Mvc\I18n\Translator($translator));
    }
}
