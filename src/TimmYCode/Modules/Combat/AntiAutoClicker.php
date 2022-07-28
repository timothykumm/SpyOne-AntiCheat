<?php

namespace TimmYCode\Modules\Combat;

use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiAutoClicker extends ModuleBase implements Module
{

	private TickUtil $hitCount;
	private float $cps = 0.0;
	private int $startTick = 0;

	public function getName() : String
	{
		return "AntiAutoklicker";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return new Message("Autoklicker detected");
	}

	public function setup(): void
	{
		$this->hitCount = new TickUtil(0);
	}

	public function checkCombat(EntityEvent $event, Player $damager, Player $target): string
	{
		if(!$this->isActive()) return "";
		$this->checkAndFirePunishment($this, $damager);

		$this->hitCount->increaseTick(1);

		if($this->hitCount->reachedTick(1)) {
			$this->startTick = PlayerUtil::getServerTick();
		} else if($this->hitCount->reachedTick(10)){
			$this->cps = (PlayerUtil::getServerTick() - $this->startTick);
			$this->hitCount->resetTick();
			$damager->sendMessage($this->cps);
			return $this->cps;
		}

		return "";
	}

	public function checkMovement(PlayerEvent $event, Player $player): String
	{
		return "";
	}

}
