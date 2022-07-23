<?php

namespace Devs\Modules\Combat;

use Devs\Modules\ModuleBase;
use Devs\Modules\Module;
use Devs\Punishment\Methods\Message;
use Devs\Punishment\Punishment;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiKillaura extends ModuleBase implements Module
{

	private array $damagerPos = array(), $targetPos = array();
	private float $distance = 0.0, $distanceAllowed = 4.1;

	public function getName() : String
	{
		return "AntiKillaura";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return new Message("Killaura detected");
	}

	public function setup(): void
	{

	}

	public function checkCombat(EntityEvent $event, Player $damager, Player $target): string
	{
		if(!$this->isActive()) return "";
		$this->checkAndFirePunishment($this, $damager);

		$this->damagerPos = PlayerUtil::getPosition($damager);
		$this->targetPos = PlayerUtil::getPosition($target);
		$this->distance = BlockUtil::calculateDistance($this->damagerPos, $this->targetPos);

		if($this->distance > $this->distanceAllowed) {
			$this->addWarning(1, $damager);
			return "Hit too far " . $this->distance;
		}
		return "";
	}

	public function checkMovement(PlayerEvent $event, Player $player): String
	{
		return "";
	}

}
