<?php

namespace Devs\Event;

use Devs\SpyOne;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

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
			/*if($output != "")
			{
				$player->sendMessage($output);
			}*/

	}

	public function onDamage(EntityDamageByEntityEvent $event) {
		$damager = $event->getDamager();
		$target = $event->getEntity();

		if(PlayerUtil::isPlayer($target->getNameTag(), $target->getId())) {
			$targetToPlayer = PlayerUtil::entityToPlayer($target->getNameTag(), $target->getId());
			PlayerUtil::addlastDamageCausedByEntityServerTick(PlayerUtil::entityToPlayer($target->getNameTag(), $target->getId()), SpyOne::getInstance()->getServer()->getTick());

			if (PlayerUtil::isPlayer($damager->getNameTag(), $damager->getId())) {
				$damagerToPlayer = PlayerUtil::entityToPlayer($damager->getNameTag(), $damager->getId());
				$playerIndex = PlayerUtil::playerExistsInArray($damagerToPlayer, WatchEventListener::$spyOnePlayerList);

				if ($playerIndex == -1) return;

				$output = WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiKillaura")->checkCombat($event, $damagerToPlayer, $targetToPlayer);

				if($output != "")
				{
					$damagerToPlayer->sendMessage($output);
				}

				//$output = WatchEventListener::$spyOnePlayerModuleList[$playerIndex]->getModule("AntiNoKnockback")->checkCombat($event, $damagerToPlayer, $targetToPlayer);


			}
		}
	}

	public function onPlayerHit(PlayerEntityInteractEvent $event) {

		//$event->getPlayer()->sendMessage($event->getClickPosition()->cross($event->getPlayer()->getPosition()->asVector3()));

	}

	public function onJump(PlayerJumpEvent $event) {
		PlayerUtil::addlastJumpServerTick(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), SpyOne::getInstance()->getServer()->getTick());
		PlayerUtil::addlastJumpPosition($event->getPlayer(), array(PlayerUtil::getX($event->getPlayer()), PlayerUtil::getY($event->getPlayer()), PlayerUtil::getZ($event->getPlayer())));

	}

	public function onDeath(PlayerRespawnEvent $event) {
		PlayerUtil::addlastRespawnServerTick(PlayerUtil::entityToPlayer($event->getPlayer()->getNameTag(), $event->getPlayer()->getId()), SpyOne::getInstance()->getServer()->getTick());
	}

}
