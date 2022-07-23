<?php

namespace Devs\Modules;

use Devs\Punishment\Punishment;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

interface Module
{

	public function getName() : String;
	public function warningLimit() : int;
	public function punishment() : Punishment;
	public function setup() : void;
	public function check(PlayerEvent $event, Player $player) : String;

}
