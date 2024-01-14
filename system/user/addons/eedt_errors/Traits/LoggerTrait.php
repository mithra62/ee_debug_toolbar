<?php

namespace DebugToolbar\Errors\Traits;

use DebugToolbar\Errors\Logging\Logger;

trait LoggerTrait
{
    /**
     * @var Logger|null
     */
    protected ?Logger $logger = null;

    /**
     * @return Logger
     */
    public function logger(): Logger
    {
        if (is_null($this->logger)) {
            $this->logger = new Logger();
        }

        $class = get_class($this);
        $this->logger->setCalledClass($class);

        return $this->logger;
    }
}