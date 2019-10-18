<?php

declare(strict_types=1);

namespace Events;

use AppendIterator;
use ArrayIterator;
use Psr\EventDispatcher\{
    EventDispatcherInterface,
    ListenerProviderInterface,
    StoppableEventInterface
};

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var ArrayIterator
     */
    protected $listeners;

    public function __construct(?ArrayIterator $listeners = null)
    {
        $this->listeners = null === $listeners ? new ArrayIterator() : $listeners;
    }

    /**
     * Register a new listener
     *
     * @param ListenerProviderInterface $listener
     */
    public function registerListener(ListenerProviderInterface $listener): void
    {
        $this->listeners->append($listener);
    }

    /**
     * Return registered listeners
     *
     * @return array<ListenerProviderInterface>
     */
    public function getListeners(): array
    {
        return $this->listeners->getArrayCopy();
    }

    /**
     * Get listeners for event as a single iterator
     *
     * @param object $event
     * @return AppendIterator
     */
    protected function getListenersForEvent(object $event): iterable
    {
        $appendIterator = new AppendIterator();

        foreach ($this->listeners as $listener) {
            $iterable = $listener->getListenersForEvent($event);

            if (is_array($iterable)) {
                $iterable = new ArrayIterator($iterable);
            }

            $appendIterator->append($iterable);
        }

        return $appendIterator;
    }

    /**
     * @inherit
     * @credit https://github.com/phly/phly-event-dispatcher
     */
    public function dispatch(object $event)
    {
        $stoppable = $event instanceof StoppableEventInterface;

        if ($stoppable && $event->isPropagationStopped()) {
            return $event;
        }

        foreach ($this->getListenersForEvent($event) as $callable) {
            $callable($event);
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }
}
