<?php

namespace TimmYCode\Modules\Other;

use pocketmine\event\Event;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\ClientUtil;
use TimmYCode\Utils\PlayerUtil;
use pocketmine\player\Player;
use TimmYCode\Utils\TickUtil;

class AntiInventoryMove extends ModuleBase implements Module
{

	private TickUtil $counter;

	public function getName() : String
	{
		return "AntiInventoryMove";
	}

	public function warningLimit(): int
	{
		return 5;
	}

	public function punishment(): Punishment
	{
		return new Message("InventoryMove detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function check(Event $event, Player $player): String
	{
		if (!$this->isActive()) return "";

		if((ClientUtil::getServerTick() - PlayerUtil::getlastInventoryTransactionTick($player)) <= 1) {
			$player->sendMessage(ClientUtil::getServerTick() - PlayerUtil::getlastInventoryTransactionTick($player));
			$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);
			return "Inventory Move";
		}

		$this->counter->increaseTick(1);
		if($this->counter->reachedTick(10)) {
			$this->resetWarning();
			$this->counter->resetTick();
		}

		return "";
	}

}
