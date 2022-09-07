<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;

class AntiStep extends ModuleBase implements Module
{
	private TickUtil $counter;
	private float $from = 0.0, $to = 0.0;
	private float $maxStep = 1.0;

	public function getName(): string
	{
		return "AntiStep";
	}

	public function getWarningLimit(): int
	{
		return 1;
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function checkA(Event $event, Player $player): string
	{
		if (!$this->isActive() || $this->getIgnored($player)) return "";

		if ($this->counter->reachedTick(0)) {
			$this->from = PlayerUtil::getY($player);
			$this->counter->increaseTick(1);
			return "";
		}

		$this->to = PlayerUtil::getY($player);
		$this->counter->resetTick();
		if (($this->to - $this->from) >= $this->maxStep) {
			if ($player->isOnGround() && !PlayerUtil::recentlyHurt($player) && !PlayerUtil::recentlyRespawned($player)) {
				if (!PlayerUtil::stepsInfluenced($player)) {
					$this->addWarning(1, $player);
					$this->checkAndFirePunishment($this, $player);
					return "Stepped up too fast";
				}
			}
		}
		return "DistanceY " . ($this->to - $this->from) . " " . $this->from . " " . $this->to;
	}

}
