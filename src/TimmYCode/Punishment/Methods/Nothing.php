<?php

namespace TimmYCode\Punishment\Methods;

use pocketmine\player\Player;
use TimmYCode\Punishment\Punishment;

class Nothing implements Punishment
{

	function __construct(string $reason)
	{

	}

	function fire(Player $player): void
	{
		//does absolutely nothing
	}

}
