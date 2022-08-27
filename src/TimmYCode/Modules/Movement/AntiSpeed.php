<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\event\Event;
use TimmYCode\Modules\ModuleBase;

use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\BlockUtil;
use TimmYCode\Utils\ClientUtil;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class AntiSpeed extends ModuleBase implements Module
{

	private TickUtil $counter;
	private array $from = array(), $to= array();
	private float $distance = -1412.0, $yDistance = 0.0, $maxDistance = 0.0;
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

	public function checkMovement(Event $event, Player $player): String
	{
		if(!$this->isActive()) return "disabled";

		$this->jumpTickDifference = (ClientUtil::getServerTick() - PlayerUtil::getlastJumpServerTick($player));

		if ($this->counter->reachedTick(10)) {
			$this->from = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player));

		} else if ($this->counter->reachedTick(20)) {
			$this->to = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player));
			$this->yDistance = ($this->to[1] - $this->from[1]);
			$this->distance = BlockUtil::calculateDistance($this->from, $this->to);

			if(PlayerUtil::movementSpeedInfluenced($player) || !BlockUtil::blockAbove($this->to, $player->getWorld())->isSameType(VanillaBlocks::AIR()) || PlayerUtil::recentlyHurt($player)) {
				$this->from = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player)); //resets fromDistance else it calculates wrong distance
				return "Movement speed influenced";
			}

			if ($this->avgOnGroundSpeed > $this->onGroundSpeedFlag) {
				$bin = $this->avgOnGroundSpeed;
				$this->onGroundReset();
				$this->addWarning(5, $player);
				//$this->counter->resetTick();
				$this->checkAndFirePunishment($this, $player);
				return $this->getWarning() . " Warnings, OnGround Speed detected. Avg: " . $bin;
			} else if ($this->avgOnGroundSpeed < $this->onGroundSpeedFlag && $this->jumpTickDifference < 20) {
				$this->onGroundReset();
			} else if ($this->yDistance <= 1 && $this->yDistance >= -1 && $this->jumpTickDifference > 20) {
				$this->avgOnGroundSpeed = (($this->avgOnGroundSpeed + $this->distance) / 2);
			} else {
				$this->onGroundReset();
			}
			//end

			$this->counter->resetTick();
			//return "Distance: ". $this->distance . " Player Ping: " . $player->getNetworkSession()->getPing() . " YDistance: " . $this->yDistance . " MovementSpeed: " . $player->getMovementSpeed();
			//$event->uncancel();
			if($this->distance > $this->maxDistance) $this->maxDistance = $this->distance;
			return "Distance " . $this->distance . " MaxDistance " . $this->maxDistance . " " . $this->yDistance;
		}
		$this->counter->increaseTick(1);
		return "";
	}

	public function onGroundReset() : void {
		$this->avgOnGroundSpeed = $this->avgOnGroundSpeedReset;
	}

}
