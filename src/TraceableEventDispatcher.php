<?php

declare(strict_types=1);

namespace Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class TraceableEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * @inherit
     */
    public function dispatch(object $event): object
    {
        $start = microtime(true);
        $eventName = get_class($event);
        $event = $this->eventDispatcher->dispatch($event);
        $this->logger->info($eventName . ' dispatch completed', [
            'duration' => microtime(true) - $start,
            'event' => $eventName,
        ]);
        return $event;
    }
}
