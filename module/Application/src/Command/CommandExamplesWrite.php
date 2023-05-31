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

use Laminas\Log\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandExamplesWrite
 * @package Application\Command
 */
class CommandExamplesWrite extends AbstractCommand
{
    protected static $defaultDescription = 'Command examples write to console.';

    /**
     * Console constructor
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        parent::__construct('app:examples-write');
        $this->logger = $logger;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->buildIo($input, $output);
        $this->writeLog('CommandExamplesWrite app:examples-write - started', Logger::NOTICE);
        $this->getIo()->title('Title example text');
        $this->getIo()->section('Section example text');
        $this->getIo()->newLine();
        $this->getIo()->note('Note example text');
        $this->getIo()->note([
            'Note 1 example text',
            'Note 2 example text',
        ]);
        $this->getIo()->newLine();
        $this->getIo()->info('Info example text');
        $this->getIo()->info([
            'Info 1 example text',
            'Info 2 example text',
        ]);
        $this->getIo()->newLine();
        $this->getIo()->warning('Warning example text');
        $this->getIo()->newLine();
        $this->getIo()->error('Error example text');
        $this->getIo()->newLine();
        $this->getIo()->caution('Caution example text');
        $this->getIo()->caution([
            'Caution 1 example text',
            'Caution 2 example text',
        ]);
        $this->getIo()->newLine();
        $this->getIo()->text('Text example text');
        $this->getIo()->newLine();
        $this->getIo()->text([
            'Text 1 example text',
            'Text 2 example text',
            'Text 3 example text',
        ]);
        $this->getIo()->newLine(2);
        $this->getIo()->listing([
            'Listing 1 example text',
            'Listing 2 example text',
            'Listing 3 example text',
        ]);
        $this->getIo()->table(
            ['Header 1', 'Header 2'],
            [
                ['Cell 1-1', 'Cell 1-2'],
                ['Cell 2-1', 'Cell 2-2'],
                ['Cell 3-1', 'Cell 3-2'],
            ]
        );
        $this->getIo()->horizontalTable(
            ['Header 1', 'Header 2'],
            [
                ['Cell 1-1', 'Cell 1-2'],
                ['Cell 2-1', 'Cell 2-2'],
                ['Cell 3-1', 'Cell 3-2'],
            ]
        );
        $this->getIo()->definitionList(
            'This is a title',
            ['foo1' => 'bar1'],
            ['foo2' => 'bar2'],
            ['foo3' => 'bar3'],
            new TableSeparator(),
            'This is another title',
            ['foo4' => 'bar4']
        );
        $this->getIo()->newLine();
//        $this->getIo()->ask('What is your name?');
        $this->getIo()->ask('Where are you from?', 'United States');
        $this->getIo()->newLine();
        $this->getIo()->ask('Number of workers to start', '1', function (mixed $number) {
            if (is_numeric($number)) {
                $number = (int)$number;
            }

            return $number;
        });
        $this->getIo()->newLine();
        $this->getIo()->confirm('Restart the web server?');
        $this->getIo()->newLine();
        $this->getIo()->choice('Select the queue to analyze', ['queue1', 'queue2', 'queue3'], 'queue1');
        $this->getIo()->newLine();
        $this->getIo()->success([
            'Lorem ipsum dolor sit amet',
            'Consectetur adipiscing elit',
        ]);

        $this->getIo()->newLine();
//        $this->getIo()->progressStart();
        $iterable = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        foreach ($this->getIo()->progressIterate($iterable, 10) as $value) {
            sleep(1);
        }
//        $this->getIo()->progressFinish();
//        $this->getIo()->newLine();
//        $this->getIo()->progressAdvance();
//        $this->getIo()->progressAdvance(10);

        $output->setFormatter(new OutputFormatter(true));
        $output->writeln(
            'The <comment>console</comment> component is'
            .' <bg=magenta;fg=cyan;option=blink>sweet!</>'
        );
        $this->getIo()->newLine();

        $rows = 10;
        $progressBar = new ProgressBar($output, $rows);
        $progressBar->setBarCharacter('<fg=magenta>=</>');
        $progressBar->setProgressCharacter("\xF0\x9F\x8D\xBA");
        $table = new Table($output);
        for ($i = 0; $i < $rows; $i++) {
            $table->addRow([
                sprintf('Row <info># %s</info>', $i),
                rand(0, 1000)
            ]);
            usleep(300000);
            $progressBar->advance();
        }
        $progressBar->finish();
        $output->writeln('');
        $table->render();

        $this->getIo()->newLine();
        $this->writeLog('CommandExamplesWrite app:examples-write - finished', Logger::NOTICE);
        $this->writeLog('ALL EXECUTED SUCCESS');

        return Command::SUCCESS;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('count', InputArgument::OPTIONAL);
        // the command help shown when running the command with the "--help" option
        $this->setHelp('This command examples write to console.');
    }
}
