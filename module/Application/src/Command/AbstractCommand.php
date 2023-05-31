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

namespace Application\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Command\Command;
use Laminas\Log\Logger;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class AbstractCommand
 * @package Application\Command
 */
class AbstractCommand extends Command
{
    public const TYPE_INTEGRATION = 'integration';

    /** @var Logger $logger */
    protected Logger $logger;

    /** @var SymfonyStyle $io */
    protected SymfonyStyle $io;

    /** @var Generator $faker */
    protected Generator $faker;

    /** @var EntityManagerInterface $entityManager */
    protected EntityManagerInterface $entityManager;

    /** @var DocumentManager $documentManager */
    protected DocumentManager $documentManager;

    /** @var ProgressBar $progressBar */
    private ProgressBar $progressBar;

    /**
     * Console constructor
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->faker = Factory::create();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function buildIo(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @param OutputInterface $output
     * @param int $count
     *
     * @return void
     */
    public function buildProgressBar(OutputInterface $output, int $count): void
    {
        $this->progressBar = new ProgressBar($output, $count);
        $this->progressBar->setBarCharacter('<fg=magenta>=</>');
        $this->progressBar->setProgressCharacter("\xF0\x9F\x8D\xBA");
    }

    /**
     * @return SymfonyStyle
     */
    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    /**
     * @return ProgressBar
     */
    public function getProgressBar(): ProgressBar
    {
        return $this->progressBar;
    }

    /**
     * @return DocumentManager
     */
    public function getDocumentManager(): DocumentManager
    {
        // @codeCoverageIgnoreStart
        return $this->documentManager;
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param string $message
     * @param int $priority
     *
     * @return void
     */
    protected function writeLog(string $message, int $priority = Logger::INFO): void
    {
        $this->logger->log($priority, $message);
        switch ($priority) {
            case Logger::ERR:
                $this->io->error($message);
                break;
            case Logger::DEBUG:
                $this->io->comment($message);
                break;
            case Logger::NOTICE:
                $this->io->note($message);
                break;
            case Logger::INFO:
            default:
                $this->io->success($message);
                break;
        }
    }

    /**
     * @param Exception $e
     *
     * @return string
     */
    public function getExceptionMessage(Exception $e): string
    {
        return 'Message - ' . $e->getMessage()
            . ', in file - ' . $e->getFile()
            . ', in line - ' . $e->getLine();
    }
}
