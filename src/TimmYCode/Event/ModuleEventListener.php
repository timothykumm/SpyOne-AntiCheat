<?php

namespace TimmYCode\Event;

use TimmYCode\SpyOne;
use TimmYCode\Utils\PlayerUtil;
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
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiStep")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed2")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiHighJump")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiGlide")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiNoKnockback")->checkMovement($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiJesus")->checkMovement($event, $player);
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
				$modifiedCooldown = PlayerUtil::getServerTick() - PlayerUtil::getlastDamageCausedByPlayerServerTick($damagerToPlayer);

				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiReach")->checkCombat($event, $damagerToPlayer, $targetToPlayer);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiKillaura")->checkCombat($event, $damagerToPlayer, $targetToPlayer);
				//WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiAutoClicker")->checkCombat($event, $damagerToPlayer, $targetToPlayer);

				if($modifiedCooldown < $actualCooldown) {
					$event->cancel();
				} else{
					PlayerUtil::addlastDamageCausedByPlayerServerTick($damagerToPlayer, SpyOne::getInstance()->getServer()->getTick());
					$output = WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiNoKnockback")->checkCombat($event, $damagerToPlayer, $targetToPlayer);
					//$output != "" ?? $damagerToPlayer->sendMessage($output);
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
