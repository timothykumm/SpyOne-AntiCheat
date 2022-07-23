<?php

namespace Devs\Event;

use Devs\SpyOne;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class ModuleEventListener implements Listener
{

	public function onMovement(PlayerMoveEvent $event) {
				$player = $event->getPlayer();
				$playerIndex = PlayerUtil::playerExistsInArray($player, WatchEventListener::$spyOnePlayerList);

				if($playerIndex == -1) return;
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiStep")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed")->check($event, $player);
				$output = WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed2")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiHighJump")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiGlide")->check($event, $player);
			/*if($output != "")
			{
				$player->sendMessage($output);
			}*/

	}

	public function onDamage(EntityDamageByEntityEvent $event) {
		PlayerUtil::addlastDamageCausedServerTick(PlayerUtil::entityToPlayer($event->getEntity()->getNameTag(), $event->getEntity()->getId()), SpyOne::getInstance()->getServer()->getTick());
	}

	public function onJump(PlayerJumpEvent $event) {
		PlayerUtil::addlastJumpServerTick(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), SpyOne::getInstance()->getServer()->getTick());
		PlayerUtil::addlastJumpPosition($event->getPlayer(), array(PlayerUtil::getX($event->getPlayer()), PlayerUtil::getY($event->getPlayer()), PlayerUtil::getZ($event->getPlayer())));

	}

	public function onDeath(PlayerRespawnEvent $event) {
		PlayerUtil::addlastRespawnServerTick(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), SpyOne::getInstance()->getServer()->getTick());
	}

}
