<?php

namespace Devs\Punishment;

use Devs\SpyOne;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\player\Player;

interface Punishment
{

	function __construct(String $reason);
	function fire(Player $player) : void;
}
