<?php

namespace TimmYCode\Modules\Combat;

use pocketmine\event\Event;
use TimmYCode\Config\ConfigManager;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Notification;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\ClientUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\player\Player;

class AntiAutoClicker extends ModuleBase implements Module
{
	private TickUtil $hitCount;
	private float $cps = 0.0;
	private int $startTick = 0;

	public function getName() : String
	{
		return "AntiAutoClicker";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return ConfigManager::getPunishment($this->getName());
	}

	public function setup(): void
	{
		$this->hitCount = new TickUtil(0);
	}

	public function checkB(Event $event, Player $damager, Player $target): string
	{
		if (!$this->isActive() || $this->getIgnored($damager)) return "";
		$this->checkAndFirePunishment($this, $damager);

		$this->hitCount->increaseTick(1);

		if($this->hitCount->reachedTick(1)) {
			$this->startTick = ClientUtil::getServerTick();
		} else if($this->hitCount->reachedTick(10)){
			$this->cps = (ClientUtil::getServerTick() - $this->startTick);
			$this->hitCount->resetTick();
			$damager->sendMessage($this->cps);
			return $this->cps;
		}

		return "";
	}

}
