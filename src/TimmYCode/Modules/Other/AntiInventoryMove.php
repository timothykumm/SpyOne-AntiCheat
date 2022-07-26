<?php

namespace TimmYCode\Modules\Other;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\PlayerUtil;


class AntiInventoryMove extends ModuleBase implements Module
{

	public function getName(): string
	{
		return "AntiInventoryMove";
	}

	public function getWarningLimit(): int
	{
		return 1;
	}

	public function setup(): void
	{

	}

	public function checkA(Event $event, Player $player): string
	{
		if (!$this->isActive() || $this->getIgnored($player)) return "";

		if (!PlayerUtil::recentlyRespawned($player) && !PlayerUtil::recentlyDied($player) && !PlayerUtil::recentlyHurt($player)) {
			$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);

			return "Inventory Move";
		}

		return "";
	}

}
