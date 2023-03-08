<?php

declare(strict_types=1);

namespace App\Component\Logs;

use App\Component\Exception\FileOpenException;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class ReadLogsFile implements ReadLogs
{
    private const CACHE_KEY = 'read.log.offset';
    private const PATH_CONFIG_KEY = 'app.log_path';
    private const FILE_CONFIG_KEY = 'app.log_file';

    private string $file;
    private int $offset = 0;
    private bool $flagToRead = true;

    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function open(ContainerBagInterface $params): void
    {
        $this->file = $params->get(self::PATH_CONFIG_KEY).$params->get(self::FILE_CONFIG_KEY);

        if (!file_exists($this->file)) {
            throw new FileOpenException(sprintf('Not fount file to path: [%s].', $this->file));
        }

        $this->offset = $this->initOffset($this->cache);
    }

    public function read(callable $call): void
    {
        $handle = fopen($this->file, 'r');
        while ($this->flagToRead && file_exists($this->file)) {
            foreach ($this->readFile($this->offset, $handle) as $value) {
                $this->offset = $value['offset'];
                $call($value['value']);
            }
        }

        fclose($handle);
    }

    public function close(): void
    {
        $this->flagToRead = false;
        $this->saveOffset($this->cache, $this->offset);
    }

    private function readFile(int $offset, $handle): \Generator
    {
        fseek($handle, $offset);
        while (!feof($handle)) {
            $get = fgets($handle);
            if (is_string($get)) {
                yield [
                    'offset' => ftell($handle),
                    'value' => trim($get),
                ];
            }
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function initOffset(CacheItemPoolInterface $cache): int
    {
        $item = $cache->getItem(self::CACHE_KEY);
        if ($item->isHit()) {
            return $item->get();
        }

        $item->set(0);
        $cache->save($item);

        return 0;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function saveOffset(CacheItemPoolInterface $cache, int $offset): void
    {
        $item = $cache->getItem(self::CACHE_KEY);
        $item->set($offset);
        $item->set(0);
        $cache->save($item);
        $cache->commit();
    }
}
