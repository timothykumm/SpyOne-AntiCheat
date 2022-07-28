<?php

namespace TimmYCode\Modules\Movement;

use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\SpyOne;
use TimmYCode\Utils\BlockUtil;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiStep extends ModuleBase implements Module
{

	private TickUtil $counter;
	private float $from = 0.0, $to = 0.0;
	private float $maxStep = 1.0;

	public function getName() : String
	{
		return "AntiStep";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return new Message("Step detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function checkCombat(EntityEvent $event, Player $damager, Player $target): string
	{
		return "";
	}

	public function checkMovement(PlayerEvent $event, Player $player): String
	{
		if(!$this->isActive()) return "";

		if($this->counter->reachedTick(0)) {
			$this->from = PlayerUtil::getY($player);
			$this->counter->increaseTick(1);
			return "";
		}

		$this->to = PlayerUtil::getY($player);
		$this->counter->resetTick();
		if(($this->to - $this->from) >= $this->maxStep) {
			if($player->isOnGround() && !PlayerUtil::recentlyHurt($player)) {
				if(!PlayerUtil::stepsInfluenced($player)) {
					$this->addWarning(1, $player);
					$this->checkAndFirePunishment($this, $player);
					return "Stepped up too fast";
				}
			}
		}
		return "DistanceY " . ($this->to - $this->from) . " " . $this->from . " " . $this->to;
	}

}
