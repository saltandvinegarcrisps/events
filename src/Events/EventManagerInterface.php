<?php

namespace Events;

interface EventManagerInterface {

	public function listen($event, callable $callable, $priority = 0);

	public function trigger($event, ...$args);

}
