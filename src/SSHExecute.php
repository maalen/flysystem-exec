<?php
/**
 * @author Maskaev Andrey <ex.maalen@gmail.com>
 * @licence GPL v3
 * @version 1.0
 */
namespace Maalen\Flysystem\Exec;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\PluginInterface;

class SSHExecute implements PluginInterface
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
     * @return string
     */
    public function handle($command, $dir='')
    {
        $adapter = $this->filesystem->getAdapter();
        $cmd = ($dir!=='')? 'cd '.$adapter->applyPathPrefix($dir).' && '.$command: $command;
        return  $adapter->execute($cmd);
    }
}
