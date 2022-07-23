<?php

namespace Devs\Punishment\Methods;

use Devs\Punishment\Punishment;
use pocketmine\player\Player;

class Kick implements Punishment
{

	private String $reason;

	function __construct(String $reason) {
		$this->reason = $reason;
	}

	function fire(Player $player): void
	{
		$player->kick($this->reason);
	}

}
