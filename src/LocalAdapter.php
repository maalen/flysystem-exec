<?php
/**
 * @author Maskaev Andrey <ex.maalen@gmail.com>
 * @licence GPL v3
 * @version 1.0
 */
namespace Maalen\Flysystem\Exec;
use League\Flysystem\Adapter\Local;
use League\Flysystem\UnreadableFileException;

/**
 * Class LocalAdapter
 * Add FOLLOW_SYMLINKS
 * @package Maalen\Flysystem\Exec
 */
class LocalAdapter extends Local
{
    /**
     * @param string $path
     * @param int    $mode
     * @return \RecursiveIteratorIterator
     */
    protected function getRecursiveDirectoryIterator($path, $mode = \RecursiveIteratorIterator::SELF_FIRST)
    {
        $opt = \FilesystemIterator::SKIP_DOTS|\FilesystemIterator::FOLLOW_SYMLINKS;
        return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, $opt), $mode);
    }
    /**
     * Normalize the file info.
     * @param \SplFileInfo $file
     * @return array
     */
    protected function normalizeFileInfo(\SplFileInfo $file)
    {
        return $this->mapFileInfo($file);
    }

    /**
     * @param \SplFileInfo $file
     * @throws UnreadableFileException
     */
    protected function guardAgainstUnreadableFileInfo(\SplFileInfo $file)
    {
        if (!$file->isReadable() && !$file->isLink()) {
            throw UnreadableFileException::forFileInfo($file);
        }
    }

    /**
     * @param \SplFileInfo $file
     * @return array
     */
    protected function mapFileInfo(\SplFileInfo $file)
    {
        $normalized = [
            'type' => $file->getType(),
            'path' => $this->getFilePath($file),
        ];
        if ($file->getRealPath()) {
            $normalized['timestamp'] = $file->getMTime();
            if ($normalized['type'] !== 'dir') {
                $normalized['size'] = $file->getSize();
            }
        }
        return $normalized;
    }

}
