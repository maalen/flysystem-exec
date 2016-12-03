<?php
/**
 * Usage local execute adapter + plugin
 * @author Maskaev Andrey <ex.maalen@gmail.com>
 * @licence GPL v3
 * @version 1.0
 */
require "vendor/autoload.php";
use League\Flysystem\Filesystem;
use Maalen\Flysystem\Exec\LocalAdapter;
use Maalen\Flysystem\Exec\LocalExecute;

$filesystem = new Filesystem(new LocalAdapter($_SERVER["HOME"]));
$filesystem->addPlugin(new LocalExecute);
$result = $filesystem->execute('ls -la');
echo "Result code: {$result['code']}".PHP_EOL;
echo "Result:".PHP_EOL;
var_dump($result['out']);
