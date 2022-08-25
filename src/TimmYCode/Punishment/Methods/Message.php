<?php

namespace TimmYCode\Punishment\Methods;

use TimmYCode\Punishment\Punishment;
use TimmYCode\SpyOne;
use pocketmine\player\Player;

class Message implements Punishment
{

	private String $reason;

	function __construct(String $reason) {
		$this->reason = $reason;
	}

	function fire(Player $player): void
	{
		$player->sendMessage(SpyOne::PREFIX . "§l§b»§r§c Warning:§e " . $this->reason);
	}

}
