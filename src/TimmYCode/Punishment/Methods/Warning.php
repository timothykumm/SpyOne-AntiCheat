<?php

namespace TimmYCode\Punishment\Methods;

use TimmYCode\Punishment\Punishment;
use TimmYCode\SpyOne;
use pocketmine\player\Player;

class Warning implements Punishment
{

	private String $reason;

	function __construct(String $reason) {
		$this->reason = $reason;
	}

	function fire(Player $player): void
	{
		$player->sendMessage(SpyOne::PREFIX . $this->reason);
	}

}
