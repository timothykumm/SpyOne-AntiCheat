<?php

declare(strict_types=1);

namespace TimmYCode\Event\Custom;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;

/**
 * Called when a player closes his inventory.
 */
class ContainerCloseEvent extends Event implements Cancellable
{
	use CancellableTrait;

	private string $playerXuid = "";
	private array $position = array();

	public function __construct(Player $player, array $position)
	{
		$this->playerXuid = $player->getXuid();
		$this->position = $position;
	}

	public function getPlayerXuid(): string
	{
		return $this->playerXuid;
	}

	public function getPosition(): array
	{
		return $this->position;
	}

}
