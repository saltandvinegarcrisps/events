<?php

declare(strict_types=1);

namespace Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
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

    /**
     * @var array<array>
     */
    protected $dispatchedEvents;

    public function __construct(EventDispatcherInterface $eventDispatcher, LoggerInterface $logger)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->dispatchedEvents = [];
    }

    /**
     * @inherit
     */
    public function dispatch(object $event): object
    {
        $eventName = get_class($event);
        $this->logger->info(sprintf('Event dispatch: %s', $eventName), ['event' => $eventName]);
        $start = microtime(true);
        $result = $this->eventDispatcher->dispatch($event);
        $duration = microtime(true) - $start;
        $this->dispatchedEvents[] = [
            'duration' => $duration,
            'event' => $eventName,
        ];
        return $result;
    }

    /**
     * Register a new listener
     */
    public function registerListener(ListenerProviderInterface $listener): void
    {
        if (!method_exists($this->eventDispatcher, 'registerListener')) {
            return;
        }
        $this->eventDispatcher->registerListener($listener);
    }

    /**
     * Events that have been called
     */
    public function getDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }

    /**
     * Reset log of Events that have been called
     */
    public function resetDispatchedEvents(): void
    {
        $this->dispatchedEvents = [];
    }
}
