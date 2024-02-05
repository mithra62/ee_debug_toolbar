<?php

namespace DebugToolbar\Services;

use ExpressionEngine\Library\Filesystem\Filesystem;

class GarbageCollectionService
{
    /**
     * How long the cache is allowed to live
     * @var int
     */
    protected int $expires = 86400;

    /**
     * Where the files live we want to monitor
     * @var string
     */
    public $cache_dir = '';

    public function __construct()
    {
        $this->cache_dir = ee('ee_debug_toolbar:ToolbarService')->getCachePath();
    }

    /**
     * Checks if a given $file has a modified date outside the $expires range
     * @param string $file
     */
    private function expired($file)
    {
        $cache_created = filemtime($file);
        $max_time = time() - $this->expires;
        if ($max_time >= $cache_created) {
            return true;
        }
    }

    /**
     * Wrapper to remove a cached file
     * @param string $file
     * @return boolean
     */
    private function delete($file)
    {
        return unlink($file);
    }

    /**
     * Do It!!
     */
    public function run()
    {
        $system = new Filesystem();
        if ($system->isWritable($this->cache_dir)) {
            $d = dir($this->cache_dir);
            while (false !== ($entry = $d->read())) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                $file = $this->cache_dir . $entry;
                if (!$system->isWritable($file)) {
                    continue; //can't write; don't care
                }

                if ($this->expired($file)) {
                    $this->delete($file);
                }
            }
            $d->close();
        }
    }
}