<?php

namespace TimmYCode\Discord;

use pocketmine\scheduler\AsyncTask;
use TimmYCode\Config\ConfigManager;

class Webhook extends AsyncTask
{

	private string $url = "";
	private string $playerName, $violation;
	private int $ping = 0;


	function __construct(string $playerName, string $violation, int $ping)
	{
		$this->playerName = $playerName;
		$this->violation = substr($violation, 4);
		$this->ping = $ping;
		if ($this->url == "") $this->url = ConfigManager::getWebhookConfiguration()["webhook"];
	}

	public function onRun(): void
	{
		$hookObject = json_encode([

			// The general "message" shown above your embeds
			//"content" => "",

			// The username shown in the message
			"username" => "SpyOne",

			// File contents to send to upload a file
			// "file" => "",

			// An array of Embeds
			"embeds" => [
				[
					// Set the title for your embed
					"title" => $this->playerName,

					// The type of your embed, will ALWAYS be "rich"
					"type" => "rich",

					// A description for your embed
					"description" => "",

					/* A timestamp to be displayed below the embed, IE for when an an article was posted
					 * This must be formatted as ISO8601
					 */
					//"timestamp" => "2021-12-10T19:15:45-05:00",

					// The integer color to be used on the left side of the embed
					"color" => hexdec("FFFFFF"),

					// Author object
					"author" => [
						"name" => "Playername:",
					],

					// Field array of objects
					"fields" => [
						// Ping
						[
							"name" => "Violation",
							"value" => $this->violation,
							"inline" => true
						],
						// Ping
						[
							"name" => "Ping",
							"value" => $this->ping,
							"inline" => true
						]
					]
				]
			]

		], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $hookObject);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
		$this->setResult([curl_exec($ch), curl_getinfo($ch, CURLINFO_RESPONSE_CODE)]);
		curl_close($ch);
	}

}