<?php

namespace TimmYCode\Modules;

use TimmYCode\Punishment\Punishment;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

interface Module
{

	public function getName() : String;
	public function warningLimit() : int;
	public function punishment() : Punishment;
	public function setup() : void;
	public function checkCombat(EntityEvent $event, Player $damager, Player $target) : String;
	public function checkMovement(PlayerEvent $event, Player $player) : String;
}
