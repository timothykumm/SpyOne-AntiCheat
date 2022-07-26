<?php

namespace Devs\Modules\Combat;

use Devs\Modules\ModuleBase;
use Devs\Modules\Module;
use Devs\Punishment\Methods\Message;
use Devs\Punishment\Punishment;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use Devs\Utils\TickUtil;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;

class AntiKillaura extends ModuleBase implements Module
{

	private TickUtil $hitCounter, $resetCounter;
	private float $distanceAlowed = 0.2;

	public function getName() : String
	{
		return "AntiKillaura";
	}

	public function warningLimit(): int
	{
		return 5;
	}

	public function punishment(): Punishment
	{
		return new Message("Killaura detected");
	}

	public function setup(): void
	{
		$this->hitCounter = new TickUtil(0);
		$this->resetCounter = new TickUtil(0);
	}

	public function checkCombat(EntityEvent $event, Player $damager, Player $target): string
	{
		if(!$this->isActive() || PlayerUtil::combatInfluenced($damager)) return "";
		if(!PlayerUtil::hasCrosshair($damager)) return "No crosshair";

		$distancePos = BlockUtil::calculateDistanceWithY(PlayerUtil::getPosition($damager), PlayerUtil::getPosition($target));
		$distanceSight = BlockUtil::calculateDistanceWithY(array(
			PlayerUtil::getX($damager) - $damager->getDirectionVector()->getX(),
				PlayerUtil::getY($damager) - $damager->getDirectionVector()->getY(),
				PlayerUtil::getZ($damager) - $damager->getDirectionVector()->getZ()),
				PlayerUtil::getPosition($target)) -1;

		if(($distancePos-$distanceSight) > $this->distanceAlowed) {
			if (!$damager->getBoundingBox()->intersectsWith(new AxisAlignedBB(PlayerUtil::getX($target) - 0.5, PlayerUtil::getY($target), PlayerUtil::getZ($target) - 0.5,
				PlayerUtil::getX($target) + 0.5, PlayerUtil::getY($target), PlayerUtil::getZ($target) + 0.5))) {
				$this->addWarning(2, $damager);
				$this->checkAndFirePunishment($this, $damager);
				//$damager->sendMessage($distancePos - $distanceSight);
				return "Killaura?";
			}
		}

		$this->setWarning(0);
		return "";
	}

	public function checkMovement(PlayerEvent $event, Player $player): String
	{
		return "";
	}

	function resetTicks() : void{
		$this->hitCounter->resetTick();
		$this->resetCounter->resetTick();
	}

}
