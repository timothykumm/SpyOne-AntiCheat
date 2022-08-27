<?php

namespace TimmYCode\Modules\Movement;

use pocketmine\event\Event;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\BlockUtil;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;

class AntiJesus extends ModuleBase implements Module
{

	private TickUtil $counter;
	private float $distance = 0.0, $allowedDistance = 3.2;
	private array $from = array(), $to = array();
	private int $allowedInAirTicks = 50;

	public function getName(): string
	{
		return "AntiJesus";
	}

	public function warningLimit(): int
	{
		return 2;
	}

	public function punishment(): Punishment
	{
		return new Message("Jesus detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function check(Event $event, Player $player): string
	{
		if (!$this->isActive()) return "";
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

			if($this->distance > $this->allowedDistance || $player->getInAirTicks() > $this->allowedInAirTicks) {
				$this->counter->resetTick();
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
