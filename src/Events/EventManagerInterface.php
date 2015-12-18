<?php

namespace Events;

interface EventManagerInterface {

	public function listen($event, $callable, $priority = 0);

	public function trigger($event);

}
