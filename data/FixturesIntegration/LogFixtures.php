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

namespace FixturesIntegration;

use Application\Document\Log;
use Carbon\Carbon;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Fixtures\AbstractFixtures;

/**
 * Auto-generated Log Fixtures for Integration tests
 * @package FixturesIntegration
 */
class LogFixtures extends AbstractFixtures implements FixtureInterface
{
    /**
     * LogFixtures construct
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct([self::INIT_MONGO_INTEGRATION, self::INIT_COUNT_LOGS_INTEGRATION]);
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        if (! empty($this->dm)) {
            $countLogs = $this->getCountLogs();
            for ($i = 1; $i <= $countLogs; $i++) {
                $priority = Log::getPriorityEven($i);
                $priorityList = Log::getPriorities();
                $priorityName = $priorityList[$priority];
                $log = $this->createLog('{orm_default} DB Connected.', $priority, $priorityName);
                $this->dm->persist($log);
            }
            $this->dm->flush();

            echo PHP_EOL
                . 'LogFixtures added ' . $countLogs . ' items for integration tests to MongoDB'
                . PHP_EOL;
        }
    }
}
