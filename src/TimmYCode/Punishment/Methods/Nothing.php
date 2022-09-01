<?php

namespace TimmYCode\Punishment\Methods;

use TimmYCode\Punishment\Punishment;
use pocketmine\player\Player;

class Nothing implements Punishment
{

	function __construct(String $reason) {

	}

	function fire(Player $player): void
	{
		//does absolutely nothing
	}

}
