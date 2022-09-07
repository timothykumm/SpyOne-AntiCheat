<?php

namespace TimmYCode\Punishment;

use pocketmine\player\Player;

interface Punishment
{
	function __construct(string $reason);

	function fire(Player $player): void;
}
