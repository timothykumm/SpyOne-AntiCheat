<?php

namespace TimmYCode\Modules;

use TimmYCode\Punishment\Punishment;

interface Module
{

	public function getName() : String;
	public function warningLimit() : int;
	public function punishment() : Punishment;
	public function setup() : void;
}
