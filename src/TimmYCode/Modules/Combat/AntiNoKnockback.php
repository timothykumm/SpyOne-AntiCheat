<?php

namespace TimmYCode\Modules\Combat;

use TimmYCode\Modules\ModuleBase;
use TimmYCode\Modules\Module;
use TimmYCode\Punishment\Methods\Message;
use TimmYCode\Punishment\Punishment;
use TimmYCode\Utils\PlayerUtil;
use TimmYCode\Utils\TickUtil;
use pocketmine\event\entity\EntityEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class AntiNoKnockback extends ModuleBase implements Module
{

	private TickUtil $counter;
	private float $knockback = 0.0, $knockbackAllowed = 0.0;
	private bool $checkMov = false;
	private Player $target;

	public function getName() : String
	{
		return "AntiNoKnockback";
	}

	public function warningLimit(): int
	{
		return 2;
	}

	public function punishment(): Punishment
	{
		return new Message("NoKnockback detected");
	}

	public function setup(): void
	{
		$this->counter = new TickUtil(0);
	}

	public function checkCombat(EntityEvent $event, Player $damager, Player $target): string
	{
		if(!$this->isActive()) return "";
		$this->checkAndFirePunishment($this, $damager);

		$this->target = $target;
		$this->checkMov = true;

		return "";
	}

	public function checkMovement(PlayerEvent $event, Player $player): String
	{
		if(!$this->checkMov) {
			$this->checkMov = false;
			return "";
		}


		$this->counter->increaseTick(1);
		$inAirTicks = $this->target->getInAirTicks();

		if($this->counter->reachedTick(1)) {
			if (PlayerUtil::knockbackInfluenced($this->target)) {
				$this->reset();
				return "Knockback influenced";
			}
		}

		if($inAirTicks != 0) {
			//$this->target->sendMessage("Knockback at tick: " . $this->counter->getTick() . " InAirTicks: " . $inAirTicks);
			$this->setWarning(0);
			$this->reset();
			return "";
		} else if($this->counter->reachedTick(10)) {
			$this->addWarning(1, $this->target);
			$this->checkAndFirePunishment($this, $this->target);
			//$player->sendMessage($this->target->getName() . " has no Knockback Ping: " . PlayerUtil::getPing($this->target));
			$this->reset();
			return "No Knockback?";
		}

		return "";
	}

	private function reset() : void {
		$this->counter->resetTick();
		$this->checkMov = false;
	}

}
