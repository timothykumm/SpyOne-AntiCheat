<?php

namespace Devs\Utils;

use Devs\SpyOne;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class TickUtil
{
	private int $tick, $rememberTick;

	function __construct($tick) {
		$this->tick = $tick;
		$this->rememberTick = $tick;
	}

	function getTick() : int
	{
		return $this->tick;
	}

	function setTick($tick) : void
	{
		$this->tick = $tick;
	}

	function increaseTick($tick) : void
	{
		$this->tick += $tick;
	}

	function reachedTick($tick) : bool
	{
		return $this->tick == $tick;
	}

	function resetTick() : void
	{
		$this->tick = $this->rememberTick;
	}

}
