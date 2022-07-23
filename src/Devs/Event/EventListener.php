<?php

namespace Devs\Event;

use Devs\Modules\ModuleBase;
use Devs\SpyOne;
use Devs\Utils\PlayerUtil;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

class EventListener implements Listener
{

	public function onMovement(PlayerMoveEvent $event) {

		$output = ModuleBase::getModule("AntiStep")->check($event);
		//$output = ModuleBase::getModule("AntiSpeed")->check($event);
		$output = ModuleBase::getModule("AntiHighJump")->check($event);
		/*if($output != "")
		{
			$event->getPlayer()->getServer()->broadcastMessage($output);
		}*/
	}

	public function onDamage(EntityDamageByEntityEvent $event) {
		PlayerUtil::addlastDamageCausedServerTick(PlayerUtil::entityToPlayer($event->getEntity()->getNameTag(), $event->getEntity()->getId()), SpyOne::getInstance()->getServer()->getTick());
	}

}
