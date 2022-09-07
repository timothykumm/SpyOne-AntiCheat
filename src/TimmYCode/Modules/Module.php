<?php

namespace TimmYCode\Modules;

interface Module
{

	public function getName(): string;

	public function getWarningLimit(): int;

	public function setup(): void;
}
