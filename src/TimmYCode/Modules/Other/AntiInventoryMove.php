<?php

namespace TimmYCode\Modules\Other;

use pocketmine\event\Event;
use TimmYCode\Config\ConfigManager;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Notification;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\PlayerUtil;
use pocketmine\player\Player;


class AntiInventoryMove extends ModuleBase implements Module
{

	public function getName() : String
	{
		return "AntiInventoryMove";
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

	}

	public function checkA(Event $event, Player $player): String
	{
		if (!$this->isActive() || $this->getIgnored($player)) return "";

		if(!PlayerUtil::recentlyRespawned($player) && !PlayerUtil::recentlyDied($player) && !PlayerUtil::recentlyHurt($player)) {
			$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);

			return "Inventory Move";
		}

		return "";
	}

}
