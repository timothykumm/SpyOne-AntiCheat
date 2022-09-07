<?php

namespace TimmYCode\Punishment\Methods;

use pocketmine\permission\BanEntry;
use pocketmine\player\Player;
use TimmYCode\Punishment\Punishment;

class Ban implements Punishment
{

	private string $reason;

	function __construct(string $reason)
	{
		$this->reason = $reason;
	}

	function fire(Player $player): void
	{
		$player->getServer()->getNameBans()->add(new BanEntry($player->getName()));
		$player->kick($this->reason);
	}

}
