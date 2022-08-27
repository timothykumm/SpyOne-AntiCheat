<?php

namespace TimmYCode\Event;

use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\DropItemAction;
use TimmYCode\SpyOne;
use TimmYCode\Utils\ClientUtil;
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
				$playerIndex = ClientUtil::playerExistsInArray($player, WatchEventListener::$spyOnePlayerList);

				if($playerIndex == -1) return;
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiStep")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiSpeed2")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiHighJump")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiGlide")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiNoKnockback")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiJesus")->check($event, $player);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiAirJump")->check($event, $player);
				//WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiInventoryMove")->check($event, $player);

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
				$playerIndex = ClientUtil::playerExistsInArray($damagerToPlayer, WatchEventListener::$spyOnePlayerList);

				if ($playerIndex == -1) return;

				$event->setAttackCooldown(0);
				$modifiedCooldown = ClientUtil::getServerTick() - PlayerUtil::getlastDamageCausedByPlayerServerTick($damagerToPlayer);

				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiReach")->check2($event, $damagerToPlayer, $targetToPlayer);
				WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiKillaura")->check2($event, $damagerToPlayer, $targetToPlayer);
				//WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiAutoClicker")->checkCombat($event, $damagerToPlayer, $targetToPlayer);

				if($modifiedCooldown < $actualCooldown) {
					$event->cancel();
				} else{
					PlayerUtil::addlastDamageCausedByPlayerServerTick($damagerToPlayer, SpyOne::getInstance()->getServer()->getTick());
					$output = WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiNoKnockback")->check2($event, $damagerToPlayer, $targetToPlayer);
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

	public function onInventoryOpen(InventoryOpenEvent $event) {
		PlayerUtil::addInventoryOpen(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), true);
	}

	public function onInventoryClose(InventoryCloseEvent $event) {
		PlayerUtil::addInventoryOpen(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), false);
	}

	public function onInventoryChange(InventoryTransactionEvent $event) {

		$player = $event->getTransaction()->getSource();
		$playerIndex = ClientUtil::playerExistsInArray($player, WatchEventListener::$spyOnePlayerList);

		if($playerIndex == -1) return;

		foreach ($event->getTransaction()->getActions() as $i) {
			if($i instanceof DropItemAction) return;
		}

		PlayerUtil::addlastInventoryTransactionServerTick($player, ClientUtil::getServerTick());

	}

}
