<?php

namespace Devs\Modules\Movement;

use Devs\Modules\ModuleBase;
use Devs\Modules\Module;
use Devs\Punishment\Methods\Message;
use Devs\Punishment\Punishment;
use Devs\Utils\PlayerUtil;
use Devs\Utils\TickUtil;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiHighJump extends ModuleBase implements Module
{

	private TickUtil $counter;
	private float $maxDistanceY = 0.754;
	private float $from = 0.0, $to = 0.0;

	public function getName() : String
	{
		return "AntiHighJump";
	}

	public function warningLimit(): int
	{
		return 2;
	}

	public function punishment(): Punishment
	{
		return new Message("High Jump detected");
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
		$this->checkAndFirePunishment($this, $player);

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

		if($distance > $this->maxDistanceY) {
			if(!$player->isOnGround() && (PlayerUtil::getServerTick() - PlayerUtil::getlastDamageCausedByEntityServerTick($player)) > 5)
			$this->addWarning(1, $player);
			return "Jumped too high";
		}

		return "DistanceY " . $distance;
	}

}
