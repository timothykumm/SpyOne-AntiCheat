<?php

namespace Devs\Punishment\Methods;

use Devs\Punishment\Punishment;
use Devs\SpyOne;
use pocketmine\player\Player;

class Message implements Punishment
{

	private String $reason;

	function __construct(String $reason) {
		$this->reason = $reason;
	}

	function fire(Player $player): void
	{
		$player->sendMessage(SpyOne::PREFIX . "Warning: " . $this->reason);
	}

}
