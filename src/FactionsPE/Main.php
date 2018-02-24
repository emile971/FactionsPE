<?php

/*
 * CLAAPI, a public api with many features for PocketMine-MP
 * Copyright (C) 2017-2018 CLADevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY;  without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

declare(strict_types = 1);

namespace FactionsPE;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\{Config, TextFormat as C};

use FactionsPE\Commands\FactionCommand;

class Main extends PluginBase {

	public function onEnable(){
		$this->registerCommands();
		$this->initConfig();
		$this->getLogger()->info(C::GREEN . "Enabled.");
	}

	public function onDisable(){
		$this->getLogger()->info(C::RED . "Disabled.");
	}

	public function registerCommands(){
		$this->getServer()->getCommandMap()->register("Factions", new FactionCommand("f", $this));
	}

	public function translate(string $totranslate){
	    $config = new Config($this->getDataFolder()."languages/".$this->getLanguage()."/"."gameplay.yml");
	    return $config->get($totranslate);
    }

    public function getLanguage(){
	    switch ($this->getConf("Language")){
            case "en":
                return "en";
                break;
            case "de":
                return "de";
            case "fr":
                return "fr";
            default:
                return "en"; //FallBack language
        }
    }

    public function getConf(string $get){
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        return $config->get($get);
    }

    public function initConfig(){
		@mkdir($this->getDataFolder());
		$this->saveResource("config.yml");
		$this->saveResource("languages/".$this->getLanguage()."/gameplay.yml");
	}
}