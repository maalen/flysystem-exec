<?php
/**
 * Usage local execute adapter + plugin
 * @author Maskaev Andrey <ex.maalen@gmail.com>
 * @licence GPL v3
 * @version 1.0
 */
require "vendor/autoload.php";
use League\Flysystem\Filesystem;
use Maalen\Flysystem\Exec\SSHAdapter;
use Maalen\Flysystem\Exec\SSHExecute;

$filesystem = new Filesystem(new SSHAdapter([
    'host' => '',
    'username' => '',
    'password' => '',
]));
$filesystem->addPlugin(new SSHExecute);
$result = $filesystem->listContents('/');
$result = $filesystem->execute('ls -la');
echo "Result code: {$result['code']}".PHP_EOL;
echo "Result:".PHP_EOL;
