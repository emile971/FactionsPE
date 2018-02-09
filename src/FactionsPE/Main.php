<?php

namespace FactionsPE;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\{Config, TextFormat};

use FactionsPE\Commands\FactionCommand;

class Main extends PluginBase{

	public function onEnable(){
		$this->RegCommands();
		$this->RegConfig();
		$this->getLogger()->info(TextFormat::GREEN . "Enabled.");
	}

	public function onDisable(){
		$this->getLogger()->info(TextFormat::RED . "Disabled.");
	}

	public function RegCommands(){
		$this->getServer()->getCommandMap()->register("F", new FactionCommand("F", $this));
	}

	public function RegConfig(){
		@mkdir($this->getDataFolder());
		$this->saveResource("languages/en/gameplay.yml");

		$this->engameplay = new Config($this->getDataFolder() . "languages/en/gameplay.yml", Config::YAML);
	}
}