<?php

namespace Devs\Punishment;

use Devs\Event\WatchEventListener;
use pocketmine\scheduler\Task;

class PunishTask extends Task
{
	public function onRun(): void
	{
		$this->checkAndFirePunishment();
	}

	private function checkAndFirePunishment(): void {
		foreach (WatchEventListener::$spyOnePlayerList as $key1 => $player) {
			foreach (WatchEventListener::$spyOnePlayerModuleList as $key2 => $modules) {
				foreach ($modules->getModuleList() as $key3 => $module) {
					if ($module->warningLimit() <= $module->getWarning()) {
						$module->punishment()->fire($player);
						$module->resetWarning();
						return;
					}
				}
			}
		}
	}

}
