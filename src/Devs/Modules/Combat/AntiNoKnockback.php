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

class AntiNoKnockback extends ModuleBase implements Module
{
	
	private float $knockback = 0.0, $knockbackAllowed = 0.0;

	public function getName() : String
	{
		return "AntiNoKnockback";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return new Message("NoKnockback detected");
	}

	public function setup(): void
	{

	}

	public function checkCombat(EntityEvent $event, Player $damager, Player $target): string
	{
		if(!$this->isActive()) return "";
		$this->checkAndFirePunishment($this, $damager);

		$this->knockback = $event->getKnockBack();

		/*if($this->distance > $this->distanceAllowed) {
			$this->addWarning(1, $damager);
			return "Hit too far " . $this->distance;
		}*/
		return $this->knockback;
	}

	public function checkMovement(PlayerEvent $event, Player $player): String
	{
		return "";
	}

}
