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

use Symfony\Component\Console\Cursor;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

require_once __DIR__ . '/../vendor/autoload.php';
$input = new ArgvInput();
$output = new ConsoleOutput();
$io = new SymfonyStyle($input, $output);
try {
    $io->title('Title example text');
    $io->section('Section example text');
    $io->newLine();
    $io->note('Note example text');
    $io->note([
        'Note 1 example text',
        'Note 2 example text',
    ]);
    $io->newLine();
    $io->info('Info example text');
    $io->info([
        'Info 1 example text',
        'Info 2 example text',
    ]);
    $io->newLine();
    $io->warning('Warning example text');
    $io->newLine();
    $io->error('Error example text');
    $io->newLine();
    $io->caution('Caution example text');
    $io->caution([
        'Caution 1 example text',
        'Caution 2 example text',
    ]);
    $io->newLine();
    $io->text('Text example text');
    $io->newLine();
    $io->text([
        'Text 1 example text',
        'Text 2 example text',
        'Text 3 example text',
    ]);
    $io->newLine(2);
    $io->listing([
        'Listing 1 example text',
        'Listing 2 example text',
        'Listing 3 example text',
    ]);
    $io->table(
        ['Header 1', 'Header 2'],
        [
            ['Cell 1-1', 'Cell 1-2'],
            ['Cell 2-1', 'Cell 2-2'],
            ['Cell 3-1', 'Cell 3-2'],
        ]
    );
    $io->horizontalTable(
        ['Header 1', 'Header 2'],
        [
            ['Cell 1-1', 'Cell 1-2'],
            ['Cell 2-1', 'Cell 2-2'],
            ['Cell 3-1', 'Cell 3-2'],
        ]
    );
    $io->definitionList(
        'This is a title',
        ['foo1' => 'bar1'],
        ['foo2' => 'bar2'],
        ['foo3' => 'bar3'],
        new TableSeparator(),
        'This is another title',
        ['foo4' => 'bar4']
    );
    $io->newLine();
//        $io->ask('What is your name?');
    $io->ask('Where are you from?', 'United States');
    $io->newLine();
    $io->ask('Number of workers to start', '1', function ($number) {
        if (! is_numeric($number)) {
            throw new \RuntimeException('You must type a number.');
        }

        return (int) $number;
    });
    $io->newLine();
    $io->confirm('Restart the web server?');
    $io->newLine();
    $io->choice('Select the queue to analyze', ['queue1', 'queue2', 'queue3'], 'queue1');
    $io->newLine();
    $io->success([
        'Lorem ipsum dolor sit amet',
        'Consectetur adipiscing elit',
    ]);

    $io->newLine();

    $cursor = new Cursor($io);
    $cursor->clearScreen();
    for ($x = 0; $x <= 10; $x++) {
        for ($y = 0; $y <= 10; $y++) {
            $cursor->moveToPosition($x, $y);
            if ($y === $x) {
                $io->write(".");
            }
        }
    }
    $io->newLine();


//        $io->progressStart();
    $iterable = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
    foreach ($io->progressIterate($iterable, 10) as $value) {
        sleep(1);
    }
//        $io->progressFinish();
//        $io->newLine();
//        $io->progressAdvance();
//        $io->progressAdvance(10);

    $output->setFormatter(new OutputFormatter(true));
    $output->writeln(
        'The <comment>console</comment> component is'
        .' <bg=magenta;fg=cyan;option=blink>sweet!</>'
    );
    $io->newLine();

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

    $io->newLine();

    $progressBar = new ProgressBar($io, 10000);
    $progressBar->setFormat('<comment>%current%/%max% [%bar%] %percent:3s%%</comment>
                    <info>%elapsed:6s%/%estimated:-6s%</info> <error>%memory:6s%</error>');
    $progressBar->start();
    for ($i = 0; $i < 10000; $i++) {
        $progressBar->advance();
        usleep(420);
    }
    $progressBar->finish();
    $io->newLine();

    $io->note('Finish');
    $io->success('ALL EXECUTED SUCCESS');
} catch (Exception $e) {
    $io->error('Error: Message - ' . $e->getMessage()
        . ', in file - ' . $e->getFile()
        . ', in line - ' . $e->getLine());
}
