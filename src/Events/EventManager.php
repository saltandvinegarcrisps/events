<?php

namespace Events;

class EventManager implements \Countable {

	protected $stack;

	public function listen($event, $callback) {
		$this->stack[$event][] = $callback;
	}

	public function dispatch($event, Event $param = null) {
		$param = null === $param ? new GenericEvent : $param;

		foreach($this->stack[$event] as $listener) {
			call_user_func($listener, $param);
		}
	}

	public function count() {
		$count = 0;

		foreach($this->stack as $events) {
			$count += count($events);
		}

		return $count;
	}

}
