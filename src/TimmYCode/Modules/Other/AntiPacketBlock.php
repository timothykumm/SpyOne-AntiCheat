<?php

namespace TimmYCode\Modules\Other;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;


class AntiPacketBlock extends ModuleBase implements Module
{

	public function getName(): string
	{
		return "AntiPacketBlock";
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

		$this->addWarning(1, $player);
		$this->checkAndFirePunishment($this, $player);

		return "Client blocked package";
	}

}
