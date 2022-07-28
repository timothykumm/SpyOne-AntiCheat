<?php

namespace TimmYCode\Utils;

use pocketmine\block\Block;
use pocketmine\world\World;

class BlockUtil
{

	static function calculateDistance(array $vector1, array $vector2): float
	{
		return sqrt(pow($vector1[0] - $vector2[0], 2) + pow(0, 2) + pow($vector1[2] - $vector2[2], 2));
	}

	static function calculateDistanceWithY(array $vector1, array $vector2): float
	{
		return sqrt(pow($vector1[0] - $vector2[0], 2) + pow($vector1[1] - $vector2[1], 2) + pow($vector1[2] - $vector2[2], 2));
	}

	static function blockUnder(array $vector1, World $world): Block
	{
		return $world->getBlockAt($vector1[0], $vector1[1]-1, $vector1[2]);
	}

	static function blockAbove(array $vector1, World $world): Block
	{
		return $world->getBlockAt($vector1[0], $vector1[1]+2, $vector1[2]);
	}

	static function blockAroundBlock(array $vector1, World $world, int $searchRadiusX, int $searchRadiusY, int $searchRadiusZ, Block $block): bool
	{
		for($x=$vector1[0]-$searchRadiusX; $x < $vector1[0]+$searchRadiusX; $x++) {
			for($y=$vector1[1]-$searchRadiusY; $y < $vector1[1]+$searchRadiusY; $y++) {
				for($z=$vector1[2]-$searchRadiusZ; $z < $vector1[2]+$searchRadiusZ; $z++) {
					if($world->getBlockAt($x, $y, $z)->isSameType($block)) {
						return true;
					}
				}
			}
		}
		return false;
	}

	static function blockAroundString(array $vector1, World $world, int $searchRadiusX, int $searchRadiusY, int $searchRadiusZ, String $block): bool
	{
		for($x=$vector1[0]-$searchRadiusX; $x < $vector1[0]+$searchRadiusX; $x++) {
			for($y=$vector1[1]-$searchRadiusY; $y < $vector1[1]+$searchRadiusY; $y++) {
				for($z=$vector1[2]-$searchRadiusZ; $z < $vector1[2]+$searchRadiusZ; $z++) {
					if(str_ends_with($world->getBlockAt($x, $y, $z)->getName(), "Stairs")) {
						return true;
					}
				}
			}
		}
		return false;
	}

}
