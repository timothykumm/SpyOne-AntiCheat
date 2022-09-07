<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;

class AntiAirJump extends ModuleBase implements Module
{
	private TickUtil $counter;
	private float $jumpDistanceY = 0.42;
	private float $from = 0.0, $to = 0.0;

	public function getName(): string
	{
		return "AntiAirJump";
	}

	public function getWarningLimit(): int
	{
		return 2;
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function checkA(Event $event, Player $player): string
	{
		if (!$this->isActive() || $this->getIgnored($player)) return "";

		if (PlayerUtil::jumpHeightInfluenced($player)) {
			return "Jump height influenced";
		}

		if ($this->counter->reachedTick(0)) {
			$this->from = PlayerUtil::getY($player);
			$this->counter->increaseTick(1);
			return "";
		}

		$this->to = PlayerUtil::getY($player);
		$this->counter->resetTick();

		$distance = ($this->to - $this->from);

		if ($distance < $this->jumpDistanceY + 0.02 && $distance > $this->jumpDistanceY - 0.02) {
			if ($player->getInAirTicks() > 6 && !PlayerUtil::recentlyHurt($player) && !PlayerUtil::recentlyRespawned($player)) {
				$this->addWarning(1, $player);
				$this->checkAndFirePunishment($this, $player);
				return "Jumped mid air";
			}
		}

		return "DistanceY " . $distance;
	}

}
