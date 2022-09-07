<?php

namespace TimmYCode\Modules\Combat;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\BlockUtil;
use TimmYCode\Utils\PlayerUtil;

class AntiReach extends ModuleBase implements Module
{

	private array $damagerPos = array(), $targetPos = array();
	private float $distance = 0.0, $distanceAllowed = 4.45, $distanceAllowedKnockback = 4.7;

	public function getName(): string
	{
		return "AntiReach";
	}

	public function getWarningLimit(): int
	{
		return 1;
	}

	public function setup(): void
	{

	}

	public function checkB(Event $event, Player $damager, Player $target): string
	{
		if (!$this->isActive() || $this->getIgnored($damager) || PlayerUtil::combatInfluenced($damager)) return "";

		$this->damagerPos = PlayerUtil::getPosition($damager);
		$this->targetPos = PlayerUtil::getPosition($target);
		$this->distance = BlockUtil::calculateDistance($this->damagerPos, $this->targetPos);


		if (($this->distance > $this->distanceAllowed && $target->getInAirTicks() <= 0) || ($this->distance > $this->distanceAllowedKnockback && $target->getInAirTicks() > 0)) {
			$this->addWarning(1, $damager);
			$this->checkAndFirePunishment($this, $damager);
			return "Reach?";
		}
		return "";
	}

}
