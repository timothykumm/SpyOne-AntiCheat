<?php

namespace TimmYCode\Modules\Movement;

use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiAirJump extends ModuleBase implements Module
{

	private TickUtil $counter;
	private float $jumpDistanceY = 0.42;
	private float $from = 0.0, $to = 0.0;

	public function getName() : String
	{
		return "AntiAirJump";
	}

	public function warningLimit(): int
	{
		return 2;
	}

	public function punishment(): Punishment
	{
		return new Message("Air Jump detected");
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
		if (!$this->isActive()) return "";

		if(PlayerUtil::jumpHeightInfluenced($player)) {
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

		if($distance < $this->jumpDistanceY + 0.02 && $distance > $this->jumpDistanceY - 0.02) {
			if(!$player->isOnGround() && (PlayerUtil::getServerTick() - PlayerUtil::getlastDamageCausedByEntityServerTick($player)) > 5 && $player->getInAirTicks() > 4)
				$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);
			return "Jumped mid air";
		}

		return "DistanceY " . $distance;
	}

}
