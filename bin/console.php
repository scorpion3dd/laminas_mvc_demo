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

use Application\Command\ArgvInput;
use Application\Command\ContainerResolver;
use Laminas\Cli\ApplicationFactory;

if (! in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
    echo 'Warning: The console should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
}
set_time_limit(0);
if (file_exists($a = __DIR__ . '/../vendor/autoload.php')) {
    require $a;
} else {
    fwrite(STDERR, 'Cannot locate autoloader; please run "composer install"' . PHP_EOL);
    exit(1);
}
$input = new ArgvInput();
if (null !== $env = $input->pullToken('--env')) {
    putenv('APP_ENV=' . $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $env);
}
if ($input->hasParameterOption('--no-debug', true)) {
    putenv('APP_DEBUG=' . $_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = '0');
}
if (isset($_SERVER['APP_DEBUG'])) {
    umask(0000);
}
// Set the main application directory as the current working directory
chdir(dirname($a) . '/../');
$applicationFactory = new ApplicationFactory();
$applicationConfig = 'config/application.config.php';
if (isset($env) && $env == 'integration') {
    $applicationConfig = 'config/application.config.test.php';
}
exit($applicationFactory(ContainerResolver::resolve($applicationConfig))->run());
