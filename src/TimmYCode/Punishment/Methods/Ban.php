<?php

namespace TimmYCode\Punishment\Methods;

use TimmYCode\Punishment\Punishment;
use pocketmine\permission\BanEntry;
use pocketmine\player\Player;

class Ban implements Punishment
{

	private String $reason;

	function __construct(String $reason) {
		$this->reason = $reason;
	}

	function fire(Player $player): void
	{
		$player->getServer()->getNameBans()->add(new BanEntry($player->getName()));
		$player->kick("Banned for cheating: " . $this->reason);
	}

}
