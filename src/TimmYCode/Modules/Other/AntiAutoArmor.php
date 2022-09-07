<?php

namespace TimmYCode\Modules\Other;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\ClientUtil;
use TimmYCode\Utils\PlayerUtil;


class AntiAutoArmor extends ModuleBase implements Module
{

	public function getName(): string
	{
		return "AntiAutoArmor";
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

		PlayerUtil::addlastInventoryContentChange($player, ClientUtil::getServerTick(), 1);

		//horion autoarmor
		/*if (PlayerUtil::getlastInventoryContentChangeTick($player) > 4) {
			$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);
		}*/

		//false positive if player right clicks hotbar armor
		//if(!PlayerUtil::isInventoryOpened($player->getXuid())) { }

		return "Auto Armor";
	}

}
