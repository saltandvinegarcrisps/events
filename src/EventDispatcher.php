<?php

declare(strict_types=1);

namespace Events;

use AppendIterator;
use ArrayIterator;
use Iterator;
use Psr\EventDispatcher\{
    EventDispatcherInterface,
    ListenerProviderInterface,
    StoppableEventInterface
};

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var ArrayIterator<int, ListenerProviderInterface>
     */
    protected $listeners;

    /**
     * @param null|ArrayIterator<int, ListenerProviderInterface> $listeners
     */
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

            if ($iterable instanceof Iterator) {
                $appendIterator->append($iterable);
            }
        }

        return $appendIterator;
    }

    /**
     * @inherit
     * @see https://github.com/phly/phly-event-dispatcher
     */
    public function dispatch(object $event): object
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
