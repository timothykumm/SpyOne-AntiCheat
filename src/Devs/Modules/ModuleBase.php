<?php

namespace Devs\Modules;

use Devs\Modules\Combat\AntiReach;
use Devs\Modules\Movement\AntiGlide;
use Devs\Modules\Movement\AntiHighJump;
use Devs\Modules\Movement\AntiSpeed;
use Devs\Modules\Movement\AntiSpeed2;
use Devs\Modules\Movement\AntiStep;
use Devs\Utils\PlayerUtil;
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
			"AntiReach" => new AntiReach()
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

}
