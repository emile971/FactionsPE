<?php

namespace FactionsPE\Commands;

use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\utils\TextFormat;

use FactionsPE\Main;

class FactionCommand extends PluginCommand{

	public function __construct($name, Main $plugin){
		parent::__construct($name, $plugin);
		$this->setDescription("Show you factions commands.");
		$this->setAliases(["f"]);
	}

	public function execute(CommandSender $sender, string $alias, array $args){
		$plugin = $this->getPlugin();

		if(count($args) < 1){
			$sender->sendMessage("{$this->getPlugin()->engameplay->get("command-usage")}");
			return true;
		}

		switch($args[0]){
			case "test":
			$sender->sendMessage("test work");
			break;
			
			default:
			$sender->sendMessage("{$this->getPlugin()->engameplay->get("command-usage")}");
			break;
		}
		return true;
	}
}