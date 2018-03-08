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

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{Config, TextFormat as C};

use FactionsPE\Commands\FactionCommand;

class FactionsPE extends PluginBase {

	public function onEnable(){
		$this->registerCommands();
		$this->initConfig();
		$this->getLogger()->info(C::GREEN . "Enabled.");
	}

    private function initConfig() : void{
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."factions/");
        $this->saveResource("config.yml");
        $this->saveResource("languages/".$this->getLanguage()."/gameplay.yml");
    }

    public function getConf(string $get){
        $config = new Config($this->getDataFolder()."config.yml", Config::YAML);
        return $config->get($get);
    }

	public function registerCommands() : void{
		$this->getServer()->getCommandMap()->register("Factions", new FactionCommand("f", $this));
	}

	public function translate(string $totranslate){
	    $config = new Config($this->getDataFolder()."languages/".$this->getLanguage()."/"."gameplay.yml");
	    return $config->get($totranslate);
    }

    public function getLanguage() : string{
	    switch ($this->getConf("Language")){
            case "en":
                return "en";
            case "de":
                return "de";
            case "fr":
                return "fr";
            default:
                return "en"; //FallBack language
        }
    }

    public function initPConfig(Player $player) : Config{
        $pconfig = new Config($this->getDataFolder()."players/".$player->getName().".yml", Config::YAML, [
            "Faction" => [],
            "Kills" => 0,
            "Deaths" => 0
        ]);
        return $pconfig;
    }

    public function getPConf(Player $player, string $get){
        $pconfig = new Config($this->getDataFolder()."players/".$player->getName().".yml");
        return $pconfig->get($get);
    }

    public function createFaction(string $name, Player $player) : Config{
        $config = new Config($this->getDataFolder()."factions/".$name.".yml", Config::YAML, [
            "FName" => $name,
            "FLeader" => $player->getName(),
            "FMembers" => array($player->getName())
        ]);
        return $config;
    }

    public function deleteFaction(Player $player){
	    if ($this->isFactionLeader($player)){
	        if (unlink($this->getDataFolder()."factions/".$this->getFactionName($player).".yml")){
	            $player->sendMessage($this->translate("faction-deleted"));
            }
        }else{
	        $player->sendMessage($this->translate("not-faction-leader"));
        }
    }

    public function setPFaction(Player $player, string $name){
	    $pconfig = new Config($this->getDataFolder()."players/".$player->getName().".yml");
	    $pconfig->set("Faction", $name);
    }

    public function getFaction(Player $player, string $get){
        $faction = new Config($this->getDataFolder()."factions/".$this->getFactionName($player).".yml");
        return $faction->get($get);
    }

    public function getFactionName(Player $player) : string{
	    return (string) $this->getPConf($player, "Faction");
    }

    public function isFactionLeader(Player $player) : bool{
	    if ($this->getFaction($player, "FLeader") == $player->getName()){
	        return true;
        }else{
	        return false;
        }
    }

    public function isInSameFaction(Player $player, Player $other) : bool{
	    if ($this->getFactionName($player) == $this->getFactionName($other)){
	        return true;
        }else{
	        return false;
        }
    }

    public function onDisable(){
        $this->getLogger()->info(C::RED . "Disabled.");
    }
}