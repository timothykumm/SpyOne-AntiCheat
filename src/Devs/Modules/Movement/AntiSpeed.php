<?php

namespace Devs\Modules\Movement;

use Devs\Modules\ModuleBase;

use Devs\Modules\Module;
use Devs\Punishment\Methods\Kick;
use Devs\Punishment\Methods\Message;
use Devs\Punishment\Punishment;
use Devs\SpyOne;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use Devs\Utils\TickUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiSpeed extends ModuleBase implements Module
{

	private TickUtil $counter;
	private array $from = array(), $from2 = array(), $to= array(), $to2 = array();
	private float $distanceMoveFlag = 5.0, $distance = -1412.0, $distance2 = -1412.0, $distancePerTick = 0.354;
	private float $heightFallenFlag = -3.0 , $yDistance = 0.0, $yDistance2 = 0.0;
	private float $previousDistance = -1141.0, $prePreviousDistance = -311.0, $maxDistance = 0.0;
	private float $onGroundSpeedFlag = 3.6, $avgOnGroundSpeed = 2.8, $avgOnGroundSpeedReset = 2.8;
	private int $jumpTickDifference = 0;

	public function getName() : String
	{
		return "AntiSpeed";
	}

	public function warningLimit(): int
	{
		return 5;
	}

	public function punishment(): Punishment
	{
		return new Message("OnGround Speed detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function checkCombat(EntityEvent $event, Player $damager, Player $target): string
	{
		return "";
	}

	public function checkMovement(PlayerEvent $event, Player $player): String
	{
		if(!$this->isActive()) return "disabled";
		$this->checkAndFirePunishment($this, $player);

		$this->jumpTickDifference = (SpyOne::getInstance()->getServer()->getTick() - PlayerUtil::getlastJumpServerTick($player));

		if ($this->counter->reachedTick(10)) {
			$this->from = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player));

		} else if ($this->counter->reachedTick(20)) {
			$this->to = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player));
			$this->yDistance = ($this->to[1] - $this->from[1]);
			$this->distance = BlockUtil::calculateDistance($this->from, $this->to);

			if(PlayerUtil::movementSpeedInfluenced($player) || !BlockUtil::blockAbove($this->to, $player->getWorld())->isSameType(VanillaBlocks::AIR())) {
				$this->resetDistances(); //prevents high distance being calculated after movement is not influenced anymore
				$this->from = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player)); //resets fromDistance else it calculates wrong distance
				return "Movement speed influenced";
			}

			if ($this->avgOnGroundSpeed > $this->onGroundSpeedFlag) {
				$bin = $this->avgOnGroundSpeed;
				$this->onGroundReset();
				$this->addWarning(5, $player);
				//$this->counter->resetTick();
				return $this->getWarning() . " Warnings, OnGround Speed detected. Avg: " . $bin;
			} else if ($this->avgOnGroundSpeed < $this->onGroundSpeedFlag && $this->jumpTickDifference < 20) {
				$this->onGroundReset();
			} else if ($this->yDistance <= 1 && $this->yDistance >= -1 && $this->jumpTickDifference > 20) {
				$this->avgOnGroundSpeed = (($this->avgOnGroundSpeed + $this->distance) / 2);
			} else {
				$this->onGroundReset();
			}
			//end

			$this->setDistances();
			$this->counter->resetTick();
			//return "Distance: ". $this->distance . " Player Ping: " . $player->getNetworkSession()->getPing() . " YDistance: " . $this->yDistance . " MovementSpeed: " . $player->getMovementSpeed();
			//$event->uncancel();
			if($this->distance > $this->maxDistance) $this->maxDistance = $this->distance;
			return "Distance " . $this->distance . " MaxDistance " . $this->maxDistance . " " . $this->yDistance;
		}
		$this->counter->increaseTick(1);
		return "";
	}

	public function setDistances() : void {
		$this->prePreviousDistance = $this->previousDistance;
		$this->previousDistance = $this->distance;
	}

	public function resetDistances() : void {
		$this->prePreviousDistance = $this->previousDistance;
		$this->previousDistance = -1412.0;
	}

	public function onGroundReset() : void {
		$this->avgOnGroundSpeed = $this->avgOnGroundSpeedReset;
	}

}
