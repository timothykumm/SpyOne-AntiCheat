<?php

namespace TimmYCode\Modules\Other;

use pocketmine\event\Event;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use pocketmine\player\Player;


class AntiAutoArmor extends ModuleBase implements Module
{

	public function getName() : String
	{
		return "AntiAutoArmor";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return new Message("AutoArmor detected");
	}

	public function setup(): void
	{

	}

	public function check(Event $event, Player $player): String
	{
		if (!$this->isActive()) return "";

			$this->addWarning(1, $player);
			$this->checkAndFirePunishment($this, $player);

			return "Auto Armor";
	}

}
