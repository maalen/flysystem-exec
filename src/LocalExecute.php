<?php
/**
 * @author Maskaev Andrey <ex.maalen@gmail.com>
 * @licence GPL v3
 * @version 1.0
 */
namespace Maalen\Flysystem\Exec;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;

class LocalExecute implements PluginInterface
{
    /**
     * FilesystemInterface instance.
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * Sets the Filesystem instance.
     * @param FilesystemInterface $filesystem
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function getMethod()
    {
        return 'execute';
    }

    /**
     * Execute command
     * @param string $command executed command
     * @param string $dir execute from directory
     * @param string $stdIn STD_IN data
     * @return string
     */
    public function handle($command, $dir='/', $stdIn=null)
    {
        $directory = $this->filesystem->getAdapter()->applyPathPrefix($dir);
        $spec = [
            0 => ["pipe", "r"], // STD_IN
            1 => ["pipe", "w"], // STD_OUT
            2 => ["pipe", "w"],  // STD_ERR
        ];
        $pipes = [];
        $exitCode = null;
        $process = proc_open($command, $spec, $pipes, $directory);
        $result = ['out'=>'', 'err'=> '', 'code'=> 255];
        if (is_resource($process)) {
            if ($stdIn !== null) {
                fwrite($pipes[0], $stdIn);
            }
            fclose($pipes[0]);
            $result['out'] = trim(stream_get_contents($pipes[1]));
            fclose($pipes[1]);
            $result['err'] = trim(stream_get_contents($pipes[2]));
            fclose($pipes[2]);
            $result['code'] = proc_close($process);
        }
        return $result;
    }
}
