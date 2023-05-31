<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Zend Framework 3 Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2020-2021 scorpion3dd
 */

declare(strict_types=1);

namespace Application\Service;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Log\Logger;
use Laminas\Mvc\I18n\Translator;
use Laminas\Stdlib\AbstractOptions;
use Laminas\Validator\ValidatorInterface;

/**
 * @package Application\Service
 */
abstract class AbstractService
{
    /** @var ContainerInterface $container */
    protected ContainerInterface $container;

    /** @var EntityManager $entityManager */
    protected EntityManager $entityManager;

    /** @var Logger|null $logger */
    protected ?Logger $logger;

    /** @var Translator|null $translator */
    protected ?Translator $translator;

    /** @var AbstractOptions $options */
    protected AbstractOptions $options;

    /** @var ValidatorInterface|null */
    protected ?ValidatorInterface $validator;

    /** @var array $config */
    protected array $config;

    /**
     * AbstractService constructor
     * @param ContainerInterface $container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->translator = $this->getTranslator();
        $this->logger = $this->getLogger();
        $this->config = $this->getConfig();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * @return Logger
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLogger(): Logger
    {
        if (empty($this->logger)) {
            $this->setLogger($this->getContainer()->get('LoggerGlobal'));
        }

        return $this->logger;
    }

    /**
     * @param Logger $logger
     *
     * @return $this
     */
    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @return Translator
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTranslator(): Translator
    {
        if (empty($this->translator)) {
            $this->setTranslator($this->getContainer()->get('MvcTranslator'));
        }

        return $this->translator;
    }

    /**
     * @param Translator $translator
     *
     * @return $this
     */
    public function setTranslator(Translator $translator): self
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @param string $message
     * @param string $textDomain
     * @param string|null $locale
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function translate(string $message, string $textDomain = 'default', string $locale = null): string
    {
        $result = $this->getTranslator()->getTranslator()->translate($message, $textDomain, $locale);
        $this->getLogger()->debug(sprintf('%s = %s', $message, $result));

        return $result;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        if (empty($this->config)) {
            $this->setConfig($this->getContainer()->get('Config'));
        }

        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
}
