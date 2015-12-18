<?php

namespace Events;

class EventManager implements EventManagerInterface {

	protected $listeners = [];

	public function listen($event, callable $callable, $priority = 0) {
		// check if event queue has been created
		if(false === array_key_exists($event, $this->listeners)) {
			$this->listeners[$event] = new \SplPriorityQueue;
		}

		$this->listeners[$event]->insert($callable, $priority);
	}

	public function trigger($event) {
		$args = array_slice(func_get_args(), 1);

		foreach($this->listeners[$event] as $listener) {
			call_user_func_array($listener, $args);
		}
	}

}
