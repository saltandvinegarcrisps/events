<?php

namespace spec\Events;

use PhpSpec\ObjectBehavior;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class TraceableEventDispatcherSpec extends ObjectBehavior
{
    public function it_should_register_listeners(EventDispatcherInterface $eventDispatcher, ListenerProviderInterface $listener): void
    {
        $this->beConstructedWith($eventDispatcher);
    }
}
