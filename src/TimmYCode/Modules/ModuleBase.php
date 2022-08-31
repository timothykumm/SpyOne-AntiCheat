<?php

namespace TimmYCode\Modules;

use pocketmine\event\Event;
use TimmYCode\Modules\Combat\AntiAutoClicker;
use TimmYCode\Modules\Combat\AntiKillaura;
use TimmYCode\Modules\Combat\AntiNoKnockback;
use TimmYCode\Modules\Combat\AntiReach;
use TimmYCode\Modules\Movement\AntiAirJump;
use TimmYCode\Modules\Movement\AntiGlide;
use TimmYCode\Modules\Movement\AntiHighJump;
use TimmYCode\Modules\Movement\AntiJesus;
use TimmYCode\Modules\Movement\AntiSpeed;
use TimmYCode\Modules\Movement\AntiSpeed2;
use TimmYCode\Modules\Movement\AntiStep;
use TimmYCode\Modules\Other\AntiAutoArmor;
use TimmYCode\Modules\Other\AntiInventoryMove;
use TimmYCode\Utils\PlayerUtil;
use pocketmine\player\Player;

class ModuleBase
{
	private bool $active = true;
	private array $modules = array();
	private int $warnings = 0;

	public function loadModules(bool $setup): void {
		$this->modules = array(
			"AntiSpeed" => new AntiSpeed(),
			"AntiSpeed2" => new AntiSpeed2(),
			"AntiHighJump" => new AntiHighJump(),
			"AntiStep" => new AntiStep(),
			"AntiGlide" => new AntiGlide(),
			"AntiReach" => new AntiReach(),
			"AntiNoKnockback" => new AntiNoKnockback(),
			"AntiKillaura" => new AntiKillaura(),
			"AntiAutoClicker" => new AntiAutoClicker(),
			"AntiJesus" => new AntiJesus(),
			"AntiAirJump" => new AntiAirJump(),
			"AntiInventoryMove" => new AntiInventoryMove(),
			"AntiAutoArmor" => new AntiAutoArmor()
		);

		if($setup) $this->setupModules();
	}

	public function setupModules(): void {
		foreach ($this->modules as $key => $module){
			$module->setup();
		}
	}

	public function activate() : void {
		$this->active = true;
	}

	public function deactivate() : void {
		$this->active = false;
	}

	public function isActive() : bool {
		return $this->active;
	}

	public function getModuleList() : array {
		return $this->modules;
	}

	public function getModule($moduleName) : ?Module {
		foreach ($this->modules as $key => $value){
			if(strcmp($key, $moduleName) == 0) {
			return $value;
			}
		}
		return null;
	}

	public function checkAndFirePunishment(Module $module, Player $player): void {
			if($module->warningLimit() <= $this->warnings) {
				$module->punishment()->fire($player);
				$module->resetWarning();
			}
	}

	public function getWarning() : int {
		return $this->warnings;
	}

	public function setWarning(int $warning) : void {
		$this->warnings = $warning;
	}

	public function resetWarning() : void {
		$this->warnings = 0;
	}

	public function addWarning(int $warning, Player $player) : void {
		if(!PlayerUtil::recentlyRespawned($player)) $this->warnings += $warning;
	}

	public function check(Event $event, Player $player) : String {
		return "";
	}

	public function check2(Event $event, Player $player, Player $target) : String {
		return "";
	}

}
