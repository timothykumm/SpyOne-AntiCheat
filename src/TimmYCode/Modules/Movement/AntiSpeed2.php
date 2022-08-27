<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\event\Event;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\SpyOne;
use TimmYCode\Utils\BlockUtil;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class AntiSpeed2 extends ModuleBase implements Module
{

	private TickUtil $counter;
	private array $from = array(), $to= array();
	private float $distance = -1412.0, $distancePerTickAllowed = 0.39;
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
		return new Message("OffGround Speed detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function check(Event $event, Player $player): String
	{
		if(!$this->isActive()) return "disabled";

		$this->jumpTickDifference = (SpyOne::getInstance()->getServer()->getTick() - PlayerUtil::getlastJumpServerTick($player));

		if($player->isOnGround() && $this->counter->getTick() > 5 && $this->jumpTickDifference > 10 && $this->jumpTickDifference < 20) {

				$this->from = PlayerUtil::getlastJumpPosition($player);
				$this->to = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player));
				$this->distance = BlockUtil::calculateDistance($this->from, $this->to);
				$this->counter->resetTick();

				if($this->distance > $this->jumpTickDifference * $this->distancePerTickAllowed) {

					if(PlayerUtil::movementSpeedInfluenced($player) || !BlockUtil::blockAbove($this->to, $player->getWorld())->isSameType(VanillaBlocks::AIR()) || PlayerUtil::recentlyHurt($player)) return "Movement speed influenced";

					$this->addWarning(5, $player);
					$this->counter->resetTick();
					$this->checkAndFirePunishment($this, $player);
					return "Too fast! Distance: " . $this->distance . " DistanceTick: " . $this->distance / $this->jumpTickDifference . " DistanceAllowed: " . $this->jumpTickDifference * $this->distancePerTickAllowed;
				}

			$this->counter->resetTick();
			return "Distance: " . $this->distance . "DistanceAllowed: " . $this->jumpTickDifference * $this->distancePerTickAllowed;
		}

		$this->counter->increaseTick(1);
		return "";
	}

}
