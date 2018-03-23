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
declare(strict_types=1);

namespace FactionsPE;

use FactionsPE\Commands\FactionCommand;
use FactionsPE\Events\DamageEvent;
use FactionsPE\Events\JoinEvent;
use pocketmine\command\overload\CommandEnum;
use pocketmine\command\overload\CommandParameter;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\{
    Config, TextFormat as C
};

class FactionsPE extends PluginBase{

    public function onEnable() : void{
        $this->registerCommands();
        $this->registerCommandParameters();
        $this->getCommand("f")->setDescription("Faction Command");
        $this->initConfig();
        $this->registerEvents();
        $this->getLogger()->info(C::GREEN . "Enabled.");
    }

    private function initConfig() : void{
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "factions/");
        @mkdir($this->getDataFolder() . "players/");
        $this->saveResource("config.yml");
        $this->saveResource("languages/" . $this->getLanguage() . DIRECTORY_SEPARATOR . "gameplay.yml");
    }

    public function getConf(string $get){
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        return $config->get($get);
    }

    private function registerCommands() : void{
        $this->getServer()->getCommandMap()->register("Factions", new FactionCommand("f", $this));
    }

    private function registerCommandParameters() : void{
        if($this->getServer()->getName() == "Altay"){
            $this->getServer()->getCommandMap()->getCommand("f")->getOverload("default")->setParameter(0, new CommandParameter("args", CommandParameter::ARG_TYPE_STRING, false, CommandParameter::ARG_FLAG_ENUM, new CommandEnum("args", ["help", "create", "delete", "invite", "kick", "info"])));
        }
    }

    private function registerEvents() : void{
        $plmngr = $this->getServer()->getPluginManager();
        $plmngr->registerEvents(new JoinEvent($this), $this);
        $plmngr->registerEvents(new DamageEvent($this), $this);
    }

    public function translate(string $totranslate){
        $config = new Config($this->getDataFolder() . "languages/" . $this->getLanguage() . DIRECTORY_SEPARATOR . "gameplay.yml");
        return $config->get($totranslate);
    }

    public function getLanguage() : string{
        switch($this->getConf("Language")){
            case "en":
                return "en";
	    case "ru":
	        return "ru";
            case "de":
                return "de";
            case "fr":
                return "fr";
            default:
                return "en"; //FallBack language
        }
    }

    public function initPConfig(Player $player) : Config{
        return new Config($this->getDataFolder() . "players/" . $player->getName() . ".yml", Config::YAML, [
            "Faction" => ""
        ]);
    }

    public function getPConf(Player $player, string $get){
        $pconfig = new Config($this->getDataFolder() . "players/" . $player->getName() . ".yml");
        return $pconfig->get($get);
    }

    public function createFaction(string $name, Player $player) : Config{
        return new Config($this->getDataFolder() . "factions/" . $name . ".yml", Config::YAML, [
            "FName" => $name,
            "FLeader" => $player->getName(),
            "FMembers" => array($player->getName())
        ]);
    }

    public function deleteFaction(Player $player) : void{
        if($this->hasFaction($player)){
            if($this->isFactionLeader($player)){
                if(unlink($this->getDataFolder() . "factions/" . $this->getFactionName($player) . ".yml")){
                    $player->sendMessage($this->translate("faction-deleted"));
                    $this->setPFaction($player, "");
                }
            }else{
                $player->sendMessage($this->translate("not-faction-leader"));
            }
        }else{
            $player->sendMessage($this->translate("have-not-a-faction"));
        }
    }

    public function setPFaction(Player $player, string $name) : void{
        $pconfig = new Config($this->getDataFolder() . "players/" . $player->getName() . ".yml");
        $pconfig->set("Faction", $name);
        $pconfig->save();
    }

    public function getFaction(Player $player, string $get){
        $faction = new Config($this->getDataFolder() . "factions/" . $this->getFactionName($player) . ".yml");
        return $faction->get($get);
    }

    public function setFaction(Player $player, $get, $set){
        $faction = new Config($this->getDataFolder() . "factions/" . $this->getFactionName($player) . ".yml");
        $faction->set($get, $set);
        $faction->save();
    }

    public function getOtherFaction(string $fname, string $get){
        $config = new Config($this->getDataFolder() . "factions/" . $fname . ".yml");
        return $config->get($get);
    }

    public function getFactionName(Player $player) : string{
        return (string)$this->getPConf($player, "Faction");
    }

    public function isFactionLeader(Player $player) : bool{
        if($this->getFaction($player, "FLeader") == $player->getName()){
            return true;
        }else{
            return false;
        }
    }

    public function isInSameFaction(Player $p1, Player $p2) : bool{
        if($this->getFactionName($p1) != "" and $this->getFactionName($p2) != ""){
            if($this->getFactionName($p1) == $this->getFactionName($p2)){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function hasFaction(Player $player) : bool{
        if(!empty($this->getFactionName($player))){
            return true;
        }else{
            return false;
        }
    }

    public function getFactionInfo(Player $player) : void{
        if($this->hasFaction($player)){
            $player->sendMessage(C::DARK_GRAY . "> " . C::YELLOW . "Faction Info" . C::DARK_GRAY . " <");
            $player->sendMessage(C::GOLD . "Name: " . C::GRAY . $this->getFaction($player, "FName"));
            $player->sendMessage(C::GOLD . "Leader: " . C::GRAY . $this->getFaction($player, "FLeader"));
            //TODO $player->sendMessage(C::BLUE . "Members: " . C::GRAY . $this->getFaction($player, "FMembers"));
        }else{
            $player->sendMessage($this->translate("have-not-a-faction"));
        }
    }

    public function getOtherFactionInfo(Player $player, string $fname) : void{
        if($this->factionExist($fname)){
            $player->sendMessage(C::DARK_GRAY . "> " . C::YELLOW . "Faction Info" . C::DARK_GRAY . " <");
            $player->sendMessage(C::GOLD . "Name: " . C::GRAY . $this->getOtherFaction($fname, "FName"));
            $player->sendMessage(C::GOLD . "Leader: " . C::GRAY . $this->getOtherFaction($fname, "FLeader"));
            //TODO $player->sendMessage(C::BLUE . "Members: " . C::GRAY . $this->getOtherFaction($fname, "FMembers"));
        }else{
            $player->sendMessage($this->translate("faction-not-exist"));
        }
    }

    public function factionExist(string $fname) : bool{
        if(file_exists($this->getDataFolder() . "factions/" . $fname . ".yml")){
            return true;
        }else{
            return false;
        }
    }

    public function kickOutofFaction(Player $player, Player $who) : void{
        $this->setPFaction($who, "");
        //TODO Kick from member list $this->setFaction($player, "FMembers", "");
    }

    public function playerExist(string $name) : bool{
        if(file_exists($this->getDataFolder() . "players/" . $name . ".yml")){
            return true;
        }else{
            return false;
        }
    }

    public function setPFacNameTag(Player $player){
        if($this->getConf("faction-nametag")){
            $player->setNameTag(C::DARK_PURPLE . $this->getFactionName($player) . C::GRAY . " : " . C::WHITE . $player->getName());
        }
    }

    public function onDisable() : void{
        $this->getLogger()->info(C::RED . "Disabled.");
    }
}
