<?php

namespace TimmYCode\Punishment;

use pocketmine\player\Player;
use TimmYCode\SpyOne;
use TimmYCode\Utils\PlayerUtil;

class Notification
{

	function __construct(Player $player, string $moduleName)
	{
		foreach (SpyOne::getInstance()->getServer()->getOnlinePlayers() as $onlineplayer) {
			if ($onlineplayer->hasPermission("spyone.notify")) {
				$onlineplayer->sendMessage(SpyOne::PREFIX . $player->getNameTag() . " §7flagged for§8 -> §b" . substr($moduleName, 4) . " §7Ping [§b" . PlayerUtil::getPing($player) . "§7]");
			}
		}
	}

}
