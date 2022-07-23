<?php

namespace Devs\Modules\Movement;

use Devs\Modules\ModuleBase;
use Devs\Modules\Module;
use Devs\Punishment\Methods\Message;
use Devs\Punishment\Punishment;
use Devs\SpyOne;
use Devs\Utils\BlockUtil;
use Devs\Utils\PlayerUtil;
use Devs\Utils\TickUtil;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiStep extends ModuleBase implements Module
{

	private TickUtil $counter;
	private float $from = 0.0, $to = 0.0;
	private float $maxStep = 1.0;

	public function getName() : String
	{
		return "AntiStep";
	}

	public function warningLimit(): int
	{
		return 1;
	}

	public function punishment(): Punishment
	{
		return new Message("Step detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function check(PlayerEvent $event, Player $player): String
	{
		if(!$this->isActive()) return "";
		$this->checkAndFirePunishment($this, $player);

		if($this->counter->reachedTick(0)) {
			$this->from = PlayerUtil::getY($player);
			$this->counter->increaseTick(1);
			return "";
		}

		$this->to = PlayerUtil::getY($player);
		$this->counter->resetTick();
		if(($this->to - $this->from) >= $this->maxStep) {
			if($player->isOnGround() && (PlayerUtil::getServerTick() - PlayerUtil::getlastDamageCausedServerTick($player)) > 5) {
				if(!PlayerUtil::stepsInfluenced($player)) {
					$this->addWarning(1, $player);
					return "Stepped up too fast";
				}
			}
		}
		return "DistanceY " . ($this->to - $this->from) . " " . $this->from . " " . $this->to;
	}

}
