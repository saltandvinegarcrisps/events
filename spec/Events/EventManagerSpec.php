<?php

namespace spec\Events;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Events\MockEventListener;
use Events\GenericEvent;
use Events\Event;

class EventManagerSpec extends ObjectBehavior {

	public function it_is_initializable() {
		$this->shouldHaveType('Events\EventManager');
	}

	public function it_should_listen_for_events() {
		$this->listen('foo', function() {});
		$this->shouldHaveCount(1);
	}

	public function it_should_dispatch_events(GenericEvent $event, MockEventListener $listener) {
		$listener->doSomething(Argument::type('Events\Event'))->shouldBeCalled();
		$this->listen('foo', [$listener, 'doSomething']);
		$this->dispatch('foo', $event);
	}

}
