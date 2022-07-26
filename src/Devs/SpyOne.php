<?php

namespace Devs;

use Devs\Event\ModuleEventListener;
use Devs\Event\WatchEventListener;
use Devs\Punishment\PunishTask;
use pocketmine\plugin\PluginBase;

class SpyOne extends PluginBase
{

	const PREFIX = "§0[§bSpy§fOne§0]§b ";
	private static self $instance;

	public function onEnable(): void
	{
		self::$instance = $this;
		$this->getServer()->getPluginManager()->registerEvents(new ModuleEventListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new WatchEventListener(), $this);
		//$this->getScheduler()->scheduleRepeatingTask(new PunishTask(), 20);
	}

	public static function getInstance(): self {
		return self::$instance;
	}

}
