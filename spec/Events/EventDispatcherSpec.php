<?php

namespace spec\Events;

use ArrayIterator;
use PhpSpec\ObjectBehavior;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcherSpec extends ObjectBehavior
{
    public function it_should_register_listeners(ArrayIterator $listeners, ListenerProviderInterface $listener): void
    {
        $this->beConstructedWith($listeners);
        $listeners->append($listener)->shouldBeCalled();
        $this->registerListener($listener);
        $listeners->getArrayCopy()->shouldBeCalled()->willReturn([$listener]);
        $this->getListeners()->shouldReturn([$listener]);
    }

    public function it_should_dispatch_events_to_listeners(ArrayIterator $listeners, ListenerProviderInterface $listener, StoppableEventInterface $event): void
    {
        $listener->getListenersForEvent($event)->shouldBeCalled()->willReturn([
            function (StoppableEventInterface $event): void {
            },
        ]);

        $listeners->append($listener)->shouldBeCalled();
        $listeners->rewind()->shouldBeCalled();
        $listeners->valid()->shouldBeCalled()->willReturn(true, false);
        $listeners->current()->shouldBeCalled()->willReturn($listener);
        $listeners->next()->shouldBeCalled();

        $event->isPropagationStopped()->shouldBeCalled()->willReturn(false);

        $this->beConstructedWith($listeners);
        $this->registerListener($listener);
        $this->dispatch($event);
    }

    public function it_should_dispatch_events_to_listeners_stoppable(
        ArrayIterator $listeners,
        ListenerProviderInterface $listener1,
        ListenerProviderInterface $listener2,
        StoppableEventInterface $event
    ): void {
        $listener1->getListenersForEvent($event)->shouldBeCalled()->willReturn([
            function (StoppableEventInterface $event): void {
            },
        ]);

        $listener2->getListenersForEvent($event)->shouldBeCalled()->willReturn([
            function (StoppableEventInterface $event): void {
            },
        ]);

        $listeners->append($listener1)->shouldBeCalled();
        $listeners->append($listener2)->shouldBeCalled();

        $listeners->rewind()->shouldBeCalled();
        $listeners->valid()->shouldBeCalled()->willReturn(true, true, false);
        $listeners->current()->shouldBeCalled()->willReturn($listener1, $listener2);
        $listeners->next()->shouldBeCalled();

        $event->isPropagationStopped()->shouldBeCalled()->willReturn(false, true);

        $this->beConstructedWith($listeners);
        $this->registerListener($listener1);
        $this->registerListener($listener2);
        $this->dispatch($event)->shouldReturn($event);
    }

    public function it_should_not_dispatch_events_stopped(ArrayIterator $listeners, StoppableEventInterface $event): void
    {
        $this->beConstructedWith($listeners);
        $event->isPropagationStopped()->shouldBeCalled()->willReturn(true);
        $this->dispatch($event)->shouldReturn($event);
    }
}
