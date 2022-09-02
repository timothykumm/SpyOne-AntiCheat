<?php

namespace TimmYCode\Config;

use TimmYCode\Punishment\Methods\Ban;
use TimmYCode\Punishment\Methods\Kick;
use TimmYCode\Punishment\Methods\Nothing;
use TimmYCode\Punishment\Methods\Warning;
use TimmYCode\Punishment\Punishment;
use TimmYCode\SpyOne;

class ConfigManager
{

	static function getModuleConfiguration(String $moduleName) : array {
		return SpyOne::getInstance()->getConfig()->getAll()["spyone"]["modules"][strtolower(substr($moduleName, 4))];
	}

	static function getWebhookConfiguration() : array {
		return SpyOne::getInstance()->getConfig()->getAll()["spyone"]["discord"];
	}

	static function getPunishment(String $moduleName): Punishment {
		$punishment = SpyOne::getInstance()->getConfig()->getAll()["spyone"]["modules"][strtolower(substr($moduleName, 4))]["punishment"];
		$message = SpyOne::getInstance()->getConfig()->getAll()["spyone"]["modules"][strtolower(substr($moduleName, 4))]["message"];

		return match (strtolower($punishment)) {
			"warning" => new Warning($message),
			"kick" => new Kick($message),
			"ban" => new Ban($message),
			default => new Nothing($message),
		};
	}

}