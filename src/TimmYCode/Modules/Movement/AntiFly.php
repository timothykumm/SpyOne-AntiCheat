<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;

class AntiFly extends ModuleBase implements Module
{
	private TickUtil $counter;
	private float $distanceY = 0.0001;
	private float $from = 0.0, $to = 0.0;

	public function getName(): string
	{
		return "AntiFly";
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

		if ($player->getInAirTicks() > 10 && $distance >= $this->distanceY) {
			if (!PlayerUtil::recentlyHurt($player) && !PlayerUtil::recentlyRespawned($player)) {
				$this->addWarning(1, $player);
				$this->checkAndFirePunishment($this, $player);
				//$player->sendMessage("Distance: " . $distance . " FallDistance: " . $player->getFallDistance() . " InAirTicks: " . $player->getInAirTicks());
				return "Fly";
			}
		}

		return "DistanceY " . $distance;
	}

}
