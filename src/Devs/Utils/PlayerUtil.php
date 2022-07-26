<?php

namespace Devs\Utils;

use Devs\SpyOne;
use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\player\Player;

class PlayerUtil
{

	private static array $damageCausedByEntityServerTick = array(), $damageCausedByPlayerServerTick = array(), $jumpServerTick = array(), $deathServerTick = array(), $respawnServerTick = array(), $jumpPosition = array();

	static function getOS(Player $player) : int {
		return $player->getNetworkSession()->getPlayerInfo()->getExtraData()["DeviceOS"];
	}

	static function getPing(Player $player) : int {
		return $player->getNetworkSession()->getPing() != null ? $player->getNetworkSession()->getPing() : 0;
	}

	static function hasCrosshair(Player $player) : int {
		return self::getOS($player) == DeviceOS::NINTENDO || self::getOS($player) == DeviceOS::OSX ||
			self::getOS($player) == DeviceOS::PLAYSTATION || self::getOS($player) == DeviceOS::TVOS ||
			self::getOS($player) == DeviceOS::WIN32 || self::getOS($player) == DeviceOS::WINDOWS_10 ||
			self::getOS($player) == DeviceOS::XBOX;
	}

	static function getPosition(Player $player): array
	{
		return array(self::getX($player), self::getY($player), self::getZ($player));
	}

	static function getX(Player $player): float
	{
		return $player->getPosition()->getX();
	}

	static function getY(Player $player): float
	{
		return $player->getPosition()->getY();
	}

	static function getZ(Player $player): float
	{
		return $player->getPosition()->getZ();
	}

	static function movementSpeedInfluenced(Player $player): bool
	{
		return ($player->isFlying() || $player->isCreative() || $player->isGliding() || $player->isInsideOfSolid() || $player->getMovementSpeed() > 0.13 || (SpyOne::getInstance()->getServer()->getTick() - self::getlastDamageCausedByEntityServerTick($player)) < 30 && BlockUtil::blockAroundString(PlayerUtil::getPosition($player),  $player->getWorld(), 1, 1, 1, "Stairs"));
	}

	static function flyingInfluenced(Player $player): bool
	{
		return ($player->isFlying() || $player->isCreative() || $player->isGliding());
	}

	static function combatInfluenced(Player $player): bool
	{
		return ($player->isCreative());
	}

	static function jumpHeightInfluenced(Player $player): bool
	{
		return ($player->getJumpVelocity() > 0.42);
	}

	static function stepsInfluenced(Player $player): bool
	{
		return ($player->isFlying() || $player->isCreative() || $player->isGliding() || BlockUtil::blockAroundString(PlayerUtil::getPosition($player),  $player->getWorld(), 1, 1, 1, "Stairs"));
	}

	static function knockbackInfluenced(Player $player): bool
	{
		return ($player->isFlying() || $player->isCreative() || $player->isGliding() || $player->isInsideOfSolid() ||
			BlockUtil::blockAroundBlock(PlayerUtil::getPosition($player), $player->getWorld(), 2, 2, 2, VanillaBlocks::COBWEB())
			|| BlockUtil::blockAroundBlock(PlayerUtil::getPosition($player), $player->getWorld(), 1, 1, 1 ,VanillaBlocks::LADDER())
				|| $player->isUnderwater() || BlockUtil::blockUnder(self::getPosition($player), $player->getWorld())->isSameType(VanillaBlocks::WATER()));
	}

	static function isJumping(Player $player): bool
	{
		return $player->getJumpVelocity() == 0.42;
	}

	static function getServerTick(): int
	{
		return SpyOne::getInstance()->getServer()->getTick();
	}

	static function entityToPlayer($entityNameTag, $entityId) : ?Player
	{
		foreach (SpyOne::getInstance()->getServer()->getOnlinePlayers() as $onlinePlayer) {
			if ((strcmp($onlinePlayer->getNameTag(),$entityNameTag) == 0) && $onlinePlayer->getId() == $entityId) {
				return $onlinePlayer;
			}
		}
		return null;
	}

	static function isPlayer($entityNameTag, $entityId) : bool
	{
		foreach (SpyOne::getInstance()->getServer()->getOnlinePlayers() as $onlinePlayer) {
			if ((strcmp($onlinePlayer->getNameTag(),$entityNameTag) == 0) && $onlinePlayer->getId() == $entityId) {
				return true;
			}
		}
		return false;
	}

	static function addlastDamageCausedByEntityServerTick(Player $player, int $serverTick): void
	{
		$playerPositionInArray = self::playerXuidExistsInArray($player, self::$damageCausedByEntityServerTick);
		$playerPositionInArray != -1 ? self::$damageCausedByEntityServerTick[$player->getXuid()] = $serverTick : self::$damageCausedByEntityServerTick += [$player->getXuid() => $serverTick];
	}

	static function addlastDamageCausedByPlayerServerTick(Player $player, int $serverTick): void
	{
		$playerPositionInArray = self::playerXuidExistsInArray($player, self::$damageCausedByPlayerServerTick);
		$playerPositionInArray != -1 ? self::$damageCausedByPlayerServerTick[$player->getXuid()] = $serverTick : self::$damageCausedByPlayerServerTick += [$player->getXuid() => $serverTick];
	}

	static function addlastJumpServerTick(Player $player, int $serverTick): void
	{
		$playerPositionInArray = self::playerXuidExistsInArray($player, self::$jumpServerTick);
		$playerPositionInArray != -1 ? self::$jumpServerTick[$player->getXuid()] = $serverTick : self::$jumpServerTick += [$player->getXuid() => $serverTick];
	}

	static function addlastDeathServerTick(Player $player, int $serverTick): void
	{
		$playerPositionInArray = self::playerXuidExistsInArray($player, self::$deathServerTick);
		$playerPositionInArray != -1 ? self::$deathServerTick[$player->getXuid()] = $serverTick : self::$deathServerTick += [$player->getXuid() => $serverTick];
	}

	static function addlastRespawnServerTick(Player $player, int $serverTick): void
	{
		$playerPositionInArray = self::playerXuidExistsInArray($player, self::$respawnServerTick);
		$playerPositionInArray != -1 ? self::$respawnServerTick[$player->getXuid()] = $serverTick : self::$respawnServerTick += [$player->getXuid() => $serverTick];
	}

	static function addlastJumpPosition(Player $player, array $pos): void
	{
		$playerPositionInArray = self::playerXuidExistsInArray($player, self::$jumpServerTick);
		$playerPositionInArray != -1 ? self::$jumpPosition[$playerPositionInArray] = $pos : self::$jumpPosition[] = $pos;
	}

	static function getlastDamageCausedByEntityServerTick(Player $player): int
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$damageCausedByEntityServerTick, $player->getXuid());
		return $serverTick != null ? $serverTick : 0;
	}

	static function getlastDamageCausedByPlayerServerTick(Player $player): int
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$damageCausedByPlayerServerTick, $player->getXuid());
		return $serverTick != null ? $serverTick : 0;
	}

	static function getlastJumpServerTick(Player $player): int
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$jumpServerTick, $player->getXuid());
		return $serverTick != null ? $serverTick : 0;
	}

	static function getlastJumpPosition(Player $player): array
	{
		$jumpPosition =  ClientUtil::getValueOfArray(self::$jumpPosition, self::playerXuidExistsInArray($player, self::$jumpServerTick));
		return $jumpPosition != null ? $jumpPosition : self::getPosition($player);
	}

	static function getlastDeathServerTick(Player $player): int
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$deathServerTick, $player->getXuid());
		return $serverTick != null ? $serverTick : 0;
	}

	static function getlastRespawnServerTick(Player $player): int
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$respawnServerTick, $player->getXuid());
		return $serverTick != null ? $serverTick : 0;
	}

	static function recentlyDied(Player $player): bool
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$deathServerTick, $player->getXuid()) != null ? ClientUtil::getValueOfArray(self::$deathServerTick, $player->getXuid()) : 0;
		return (SpyOne::getInstance()->getServer()->getTick() - $serverTick) < 30;
	}

	static function recentlyHurt(Player $player): bool
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$damageCausedByEntityServerTick, $player->getXuid()) != null ? ClientUtil::getValueOfArray(self::$damageCausedByEntityServerTick, $player->getXuid()) : 0;
		return (SpyOne::getInstance()->getServer()->getTick() - $serverTick) < 30;
	}

	static function recentlyRespawned(Player $player): bool
	{
		$serverTick =  ClientUtil::getValueOfArray(self::$respawnServerTick, $player->getXuid()) != null ? ClientUtil::getValueOfArray(self::$respawnServerTick, $player->getXuid()) : 0;
		return (SpyOne::getInstance()->getServer()->getTick() - $serverTick) < 30;
	}

	static function playerExistsInArray(Player $player, array $array): int {
		$arraySize = count($array);
		$keys = array_keys($array);

		for ($x = 0; $x < $arraySize; $x++){
			if($array[$keys[$x]] == $player) {
				return $x;
			}
		}
		return -1;
	}

	static function playerXuidExistsInArray(Player $player, array $array): int {
		$arraySize = count($array);
		$keys = array_keys($array);

		for ($x = 0; $x < $arraySize; $x++){
			if($keys[$x] == $player->getXuid()) {
				return $x;
			}
		}
		return -1;
	}

}
