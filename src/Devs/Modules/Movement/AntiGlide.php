<?php

namespace Devs\Modules\Movement;

use Devs\Modules\ModuleBase;
use Devs\Modules\Module;
use Devs\Punishment\Methods\Message;
use Devs\Punishment\Punishment;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use Devs\Utils\TickUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiGlide extends ModuleBase implements Module
{

	private TickUtil $counter, $glideCounter;
	private float $from = 0.0, $to = 0.0, $fallDistance = 0.0, $previousFallDistance = -9693;
	private float $deviation = 0.001;

	public function getName() : String
	{
		return "AntiGlide";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return new Message("Glide detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
		$this->glideCounter = new TickUtil(0);
	}

	public function check(PlayerEvent $event, Player $player): String
	{
		if (!$this->isActive()) return "";
		$this->checkAndFirePunishment($this, $player);

		if ($this->counter->reachedTick(0)) {
			$this->from = PlayerUtil::getY($player);
			$this->counter->increaseTick(1);
			return "";
		}

		if($player->isOnGround() || PlayerUtil::flyingInfluenced($player)) {
			$this->resetTicks();
			$this->resetDistances();
			return "No Motion or flying influenced";
		}

		$this->to = PlayerUtil::getY($player);
		$this->counter->resetTick();

		$this->fallDistance = ($this->to - $this->from);
		$sum = $this->fallDistance - $this->previousFallDistance;
		if ($sum <= $this->deviation && $sum >= -$this->deviation && $this->to != 0.0) {

			if(BlockUtil::blockAroundBlock(PlayerUtil::getPosition($player), $player->getWorld(), 2, 2, 2, VanillaBlocks::COBWEB())
				|| BlockUtil::blockAroundBlock(PlayerUtil::getPosition($player), $player->getWorld(), 1, 1, 1 ,VanillaBlocks::LADDER())) {
			$this->resetTicks();
			$this->resetDistances();
			return "In Cobweb or on ladder";
			}

			$this->glideCounter->increaseTick(1);
		}

		$this->previousFallDistance = $this->fallDistance;

		if($this->glideCounter->reachedTick(3)) {
			$this->addWarning(1, $player);
			$this->glideCounter->resetTick();
			return "Glided?";
		}

		return $sum;
	}

	function resetDistances(): void {
		$this->fallDistance = 0.0;
		$this->previousFallDistance = 0;
	}

	function resetTicks() {
		$this->counter->resetTick();
		$this->glideCounter->resetTick();
	}

}
