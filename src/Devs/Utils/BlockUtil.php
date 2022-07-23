<?php

namespace Devs\Utils;

use Devs\SpyOne;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use pocketmine\world\World;

class BlockUtil
{

	static function calculateDistance(array $vector1, array $vector2): float
	{
		return sqrt(pow($vector1[0] - $vector2[0], 2) + pow(0, 2) + pow($vector1[2] - $vector2[2], 2));
	}

	static function blockUnder(array $vector1, World $world): Block
	{
		return $world->getBlockAt($vector1[0], $vector1[1]-1, $vector1[2]);
	}

	static function blockAbove(array $vector1, World $world): Block
	{
		return $world->getBlockAt($vector1[0], $vector1[1]+2, $vector1[2]);
	}

	static function inCobweb(array $vector1, World $world): bool
	{
		for($x=$vector1[0]-2; $x < $vector1[0]+2; $x++) {
			for($y=$vector1[1]-2; $y < $vector1[1]+2; $y++) {
				for($z=$vector1[2]-2; $z < $vector1[2]+2; $z++) {
					if($world->getBlockAt($x, $y, $z)->isSameType(VanillaBlocks::COBWEB())) {
						return true;
					}
				}
			}
		}
		return false;
	}

	static function onStairs(array $vector1, World $world): bool
	{
		for($x=$vector1[0]-2; $x < $vector1[0]+2; $x++) {
			for($y=$vector1[1]-1; $y < $vector1[1]+1; $y++) {
				for($z=$vector1[2]-2; $z < $vector1[2]+2; $z++) {
					if(str_ends_with($world->getBlockAt($x, $y, $z)->getName(), "Stairs")) {
						return true;
					}
				}
			}
		}
		return false;
	}

}
