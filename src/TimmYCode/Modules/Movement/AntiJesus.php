<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\BlockUtil;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;

class AntiJesus extends ModuleBase implements Module
{

	private TickUtil $counter, $deviationCounter;
	private float $distance = 0.0, $allowedDistance = 3.2, $deviation = 1;
	private array $from = array(), $to = array();
	private int $allowedInAirTicks = 50;

	public function getName(): string
	{
		return "AntiJesus";
	}

	public function getWarningLimit(): int
	{
		return 2;
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
		$this->deviationCounter = new TickUtil(0);
	}

	public function checkA(Event $event, Player $player): string
	{
		if (!$this->isActive() || $this->getIgnored($player)) return "";
		if (!BlockUtil::blockUnder(PlayerUtil::getPosition($player), $player->getWorld())->isSameType(VanillaBlocks::WATER())) {
			$this->counter->resetTick();
			return "Not above water";
		}

		if ($this->counter->reachedTick(10)) {
			$this->from = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player));

		} else if ($this->counter->reachedTick(20)) {
			$this->to = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player));
			$this->distance = BlockUtil::calculateDistance($this->from, $this->to);

			if (PlayerUtil::recentlyHurt($player)) {
				$this->from = array(PlayerUtil::getX($player), PlayerUtil::getY($player), PlayerUtil::getZ($player)); //resets fromDistance else it calculates wrong distance
				$this->counter->resetTick();
				return "Movement speed influenced";
			}

			$this->deviation = $this->to[1] - $this->from[1];

			if($this->deviation == 0) {
				$this->deviationCounter->increaseTick(1);
			}else{
				$this->deviationCounter->resetTick();
			}

			if ($this->distance > $this->allowedDistance || $player->getInAirTicks() > $this->allowedInAirTicks || $this->deviationCounter->reachedTick(2)) {
				$this->counter->resetTick();
				$this->deviationCounter->resetTick();
				$this->addWarning(1, $player);
				$this->checkAndFirePunishment($this, $player);
				return "Jesus?";
			}

			$this->counter->resetTick();
		}
		$this->counter->increaseTick(1);
		return "";
	}
}
