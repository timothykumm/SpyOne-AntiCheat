<?php

namespace TimmYCode\Modules\Combat;

use pocketmine\event\Event;
use pocketmine\player\Player;
use TimmYCode\Modules\Module;
use TimmYCode\Modules\ModuleBase;
use TimmYCode\Utils\PlayerUtil;

class AntiKillaura extends ModuleBase implements Module
{
	private float $lengthAlowedCrosshair = 1.2, $lengthAlowedNoCrosshair = 3.3;

	public function getName(): string
	{
		return "AntiKillaura";
	}

	public function getWarningLimit(): int
	{
		return 5;
	}

	public function setup(): void
	{

	}

	public function checkB(Event $event, Player $damager, Player $target): string
	{
		if (!$this->isActive() || $this->getIgnored($damager)) return "";

		$posFoot = $damager->getPosition()->asVector3();
		$posHead = $damager->getEyePos()->asVector3();

		$dirLooking = $damager->getDirectionVector()->normalize();
		$targetPos = $target->getPosition()->asVector3();

		$directionFoot = $posFoot->subtractVector($targetPos);
		$directionHead = $posHead->subtractVector($targetPos);

		$crossPosFoot = $directionFoot->cross($dirLooking);
		$crossPosHead = $directionHead->cross($dirLooking);

		$crossLengthFoot = $crossPosFoot->length();
		$crossLengthHead = $crossPosHead->length();

		if ($crossLengthFoot > $this->lengthAlowedCrosshair && $crossLengthHead > $this->lengthAlowedCrosshair) {
			if (PlayerUtil::hasCrosshair($damager)) {
				$this->addWarning(3, $damager);
				$this->checkAndFirePunishment($this, $damager);
				return "Killaura crosshair?";
			} else if ($crossLengthFoot > $this->lengthAlowedNoCrosshair && $crossLengthHead > $this->lengthAlowedNoCrosshair) {
				$this->addWarning(5, $damager);
				$this->checkAndFirePunishment($this, $damager);
				return "Killaura no crosshair?";
			}
		}

		$this->setWarning(0);
		return "";
	}

}
