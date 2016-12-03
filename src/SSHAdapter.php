<?php
/**
 * @author Maskaev Andrey <ex.maalen@gmail.com>
 * @licence GPL v3
 * @version 1.0
 */

namespace Maalen\Flysystem\Exec;
use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\AdapterInterface;
use phpseclib\Net\SFTP;

class SSHAdapter extends SftpAdapter
{

    /**
     * Connect.
     */
    public function connect()
    {
        $this->connection = $this->connection ?: new SFTP($this->host, $this->port, $this->timeout);
        $this->login();
        $this->setConnectionRoot();
    }

    /**
     * Set the connection root.
     */
    protected function setConnectionRoot()
    {
        $root = $this->getRoot();

        if (! $root) {
            $this->root = $this->connection->pwd() . $this->separator;
            return;
        } else {
            if (! $this->connection->chdir($root)) {
                throw new \RuntimeException('Root is invalid or does not exist: '.$root);
            }
            $this->root = $this->connection->pwd() . $this->separator;
        }
    }

    protected function normalizeListingObject($path, array $object)
    {
        $permissions = $this->normalizePermissions($object['permissions']);
        if ($object['type']===3) {
            $object = $this->connection->lstat($this->connection->readlink($path));
        }
        $type = ($object['type'] === 1) ? 'file' : 'dir';
        $timestamp = $object['mtime'];

        if ($type === 'dir') {
            return compact('path', 'timestamp', 'type');
        }

        $visibility = $permissions & 0044 ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE;
        $size = (int) $object['size'];

        return compact('path', 'timestamp', 'type', 'visibility', 'size');
    }

    public function execute($command)
    {
        return ['out' => $this->connection->exec($command), 'code'=> $this->connection->exit_status];
    }

}
