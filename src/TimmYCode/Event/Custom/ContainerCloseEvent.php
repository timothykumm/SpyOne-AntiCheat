<?php

declare(strict_types=1);

namespace TimmYCode\Event\Custom;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\player\Player;
use TimmYCode\Event\ModuleEventListener;

/**
 * Called when a player closes his inventory.
 */

class ContainerCloseEvent extends Event implements Cancellable{
	use CancellableTrait;

	private String $playerXuid = "";
	private array $position = array();

	public function __construct(Player $player, array $position){
		$this->playerXuid = $player->getXuid();
		$this->position = $position;
	}

	public function getPlayerXuid() : String{
		return $this->playerXuid;
	}

	public function getPosition() : array{
		return $this->position;
	}

}
