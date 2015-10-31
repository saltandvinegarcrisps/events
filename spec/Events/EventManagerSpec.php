<?php

namespace spec\Events;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EventManagerSpec extends ObjectBehavior {

	public function it_is_initializable() {
		$this->shouldHaveType('Events\EventManager');
	}

}
