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
<<<<<<< HEAD
use TimmYCode\Utils\TickUtil;
=======
>>>>>>> 7f90415559070669538b5ed48a901405c075ef9f

class AntiInventoryMove extends ModuleBase implements Module
{

<<<<<<< HEAD
	private TickUtil $counter;

=======
>>>>>>> 7f90415559070669538b5ed48a901405c075ef9f
	public function getName() : String
	{
		return "AntiInventoryMove";
	}

	public function warningLimit(): int
	{
<<<<<<< HEAD
		return 5;
=======
		return 1;
>>>>>>> 7f90415559070669538b5ed48a901405c075ef9f
	}

	public function punishment(): Punishment
	{
		return new Message("InventoryMove detected");
	}

	public function setup(): void
	{
<<<<<<< HEAD
		$this->counter = new TickUtil(0);
=======

>>>>>>> 7f90415559070669538b5ed48a901405c075ef9f
	}

	public function check(Event $event, Player $player): String
	{
		if (!$this->isActive()) return "";

<<<<<<< HEAD
		if((ClientUtil::getServerTick() - PlayerUtil::getlastInventoryTransactionTick($player)) <= 1) {
			$player->sendMessage(ClientUtil::getServerTick() - PlayerUtil::getlastInventoryTransactionTick($player));
=======
		if((ClientUtil::getServerTick() - PlayerUtil::getlastInventoryTransactionTick($player)) < 5) {
>>>>>>> 7f90415559070669538b5ed48a901405c075ef9f
			$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);
			return "Inventory Move";
		}

<<<<<<< HEAD
		$this->counter->increaseTick(1);
		if($this->counter->reachedTick(10)) {
			$this->resetWarning();
			$this->counter->resetTick();
		}

=======
>>>>>>> 7f90415559070669538b5ed48a901405c075ef9f
		return "";
	}

}
