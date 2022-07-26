<?php

namespace Devs\Utils;

use pocketmine\player\Player;

class ClientUtil
{

	static function getKeyOfArray(array $array, $value) {
		$arraySize = count($array);
		$keys = array_keys($array);

		for ($x = 0; $x < $arraySize; $x++){
			if(($array[$keys[$x]] == $value)) {
				return $keys[$x];
			}
		}
		return null;
	}

	static function getValueOfArray(array $array, $key) {
		$arraySize = count($array);
		$keys = array_keys($array);

		for ($x = 0; $x < $arraySize; $x++){
			if(strcmp($keys[$x], $key) == 0) {
				return $array[$keys[$x]];
			}
		}
		return null;
	}

	static function replace_key($arr, Player $value, $newkey) : array{
			$keys = array_keys($arr);
			$values = array_values($arr);

			$keys[array_search($value, $values)] = $newkey;
			return array_combine($keys, $values);
	}
}
