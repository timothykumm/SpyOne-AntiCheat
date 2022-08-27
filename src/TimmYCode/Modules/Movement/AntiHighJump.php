<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\event\Event;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\ClientUtil;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
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

	public function check(Event $event, Player $player): String
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

		if($distance > $this->maxDistanceY) {
			if(!$player->isOnGround() && (ClientUtil::getServerTick() - PlayerUtil::getlastDamageCausedByEntityServerTick($player)) > 5)
			$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);
			return "Jumped too high";
		}

		return "DistanceY " . $distance;
	}

}
