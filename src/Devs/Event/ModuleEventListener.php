<?php

namespace Devs\Event;

use Devs\SpyOne;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\EventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ModuleEventListener implements Listener
{

	public int $average = 2, $count = 1;

	public function onMovement(PlayerMoveEvent $event) {
				$player = $event->getPlayer();
				$playerIndex = PlayerUtil::playerExistsInArray($player, WatchEventListener::$spyOnePlayerList);

				if($playerIndex == -1) return;
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiStep")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed2")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiHighJump")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiGlide")->checkMovement($event, $player);
			/*if($output != "")
			{
				$player->sendMessage($output);
			}*/

	}

	public function onDamage(EntityDamageByEntityEvent $event) {
		$damager = $event->getDamager();
		$target = $event->getEntity();
		$actualCooldown = $event->getAttackCooldown();

		if(PlayerUtil::isPlayer($target->getNameTag(), $target->getId())) {
			$targetToPlayer = PlayerUtil::entityToPlayer($target->getNameTag(), $target->getId());
			PlayerUtil::addlastDamageCausedByEntityServerTick(PlayerUtil::entityToPlayer($target->getNameTag(), $target->getId()), SpyOne::getInstance()->getServer()->getTick());

			if (PlayerUtil::isPlayer($damager->getNameTag(), $damager->getId())) {
				$damagerToPlayer = PlayerUtil::entityToPlayer($damager->getNameTag(), $damager->getId());
				$playerIndex = PlayerUtil::playerExistsInArray($damagerToPlayer, WatchEventListener::$spyOnePlayerList);

				if ($playerIndex == -1) return;

				$event->setAttackCooldown(0);

				$cooldown = SpyOne::getInstance()->getServer()->getTick() - PlayerUtil::getlastDamageCausedByPlayerServerTick($damagerToPlayer);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiReach")->checkCombat($event, $damagerToPlayer, $targetToPlayer);

				if($cooldown < $actualCooldown) {
					$event->cancel();
				} else{
					PlayerUtil::addlastDamageCausedByPlayerServerTick($damagerToPlayer, SpyOne::getInstance()->getServer()->getTick());
				}


			}
		}
	}

	public function onJump(PlayerJumpEvent $event) {
		PlayerUtil::addlastJumpServerTick(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), SpyOne::getInstance()->getServer()->getTick());
		PlayerUtil::addlastJumpPosition($event->getPlayer(), array(PlayerUtil::getX($event->getPlayer()), PlayerUtil::getY($event->getPlayer()), PlayerUtil::getZ($event->getPlayer())));
	}

	public function onDeath(PlayerRespawnEvent $event) {
		PlayerUtil::addlastRespawnServerTick(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), SpyOne::getInstance()->getServer()->getTick());
	}

}
