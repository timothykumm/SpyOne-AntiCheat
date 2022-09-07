<?php

declare(strict_types=1);

namespace TimmYCode\Event\Custom;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;

/**
 * Called when a player right clicks armor!?.
 */
class InventoryContentChangeEvent extends Event implements Cancellable
{
	use CancellableTrait;

	public function __construct(Player $player)
	{
		$this->playerXuid = $player->getXuid();
	}

	public function getPlayerXuid(): string
	{
		return $this->playerXuid;
	}

}
