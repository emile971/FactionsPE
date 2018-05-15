<?php

/*
 * FactionsPE, a public Factions plugin with many features for PocketMine-MP
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

use FactionsPE\commands\FactionCommand;
use pocketmine\command\overload\CommandEnum;
use pocketmine\command\overload\CommandParameter;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class FactionsPE extends PluginBase{

    public const VERSION = "v1.0.2";
    private static $instance;

    public function onEnable() : void{
        self::$instance = $this;
        $this->registerCommands();
        $this->initConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getLogger()->info(TextFormat::GREEN . "FactionsPE " . self::VERSION . " Enabled");
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
        $cmdmap = $this->getServer()->getCommandMap();
        $cmdmap->register("Factions", new FactionCommand("f", $this));
        $cmdmap->getCommand("f")->setDescription("Factions Command.");
        if($this->getServer()->getName() == "Altay"){
            $cmdmap->getCommand("f")->getOverload("default")->setParameter(0, new CommandParameter("args", CommandParameter::ARG_TYPE_STRING, false, new CommandEnum("args", ["help", "create", "delete", "invite", "kick", "info"])));
        }
    }

    public function translate(string $totranslate){
        $config = new Config($this->getDataFolder() . "languages/" . $this->getLanguage() . DIRECTORY_SEPARATOR . "gameplay.yml");
        return $config->get($totranslate);
    }

    private function getLanguage() : string{
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
                return "en";
        }
    }

    public function initPConfig(Player $player) : Config{
        return new Config($this->getDataFolder() . "players/" . $player->getName() . ".yml", Config::YAML, [
            "Faction" => null
        ]);
    }

    private function getPConf(Player $player, string $get){
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

    public function deleteFaction(Player $player) : bool{
        if($this->hasFaction($player)){
            if($this->isFactionLeader($player)){
                if(unlink($this->getDataFolder() . "factions/" . $this->getFactionName($player) . ".yml")){
                    $player->sendMessage($this->translate("faction-deleted"));
                    $this->setPFaction($player, "");
                }
            }else{
                $player->sendMessage($this->translate("not-faction-leader"));
                return false;
            }
        }else{
            $player->sendMessage($this->translate("have-not-a-faction"));
            return false;
        }
        return true;
    }

    public function setPFaction(Player $player, $name) : void{
        $pconfig = new Config($this->getDataFolder() . "players/" . $player->getName() . ".yml");
        $pconfig->set("Faction", $name);
        $pconfig->save();
    }

    public function getFaction(Player $player, string $get){
        $faction = new Config($this->getDataFolder() . "factions/" . $this->getFactionName($player) . ".yml");
        return $faction->get($get);
    }

    public function setFaction(Player $player, $get, $set) : void{
        $faction = new Config($this->getDataFolder() . "factions/" . $this->getFactionName($player) . ".yml");
        $faction->set($get, $set);
        $faction->save();
    }

    public function getOtherFaction(string $fname, string $get){
        $config = new Config($this->getDataFolder() . "factions/" . $fname . ".yml");
        return $config->get($get);
    }

    public function getFactionName(Player $player) : string{
        return (string) $this->getPConf($player, "Faction");
    }

    public function isFactionLeader(Player $player) : bool{
        return $this->getFaction($player, "FLeader") == $player->getName() ? true : false;
    }

    public function isInSameFaction(Player $p1, Player $p2) : bool{
        if(!empty($this->getFactionName($p1)) and !empty($this->getFactionName($p2))){
            return $this->getFactionName($p1) == $this->getFactionName($p2) ? true : false;
        }else{
            return false;
        }
    }

    public function hasFaction(Player $player) : bool{
        return !empty($this->getFactionName($player));
    }

    public function getFactionInfo(Player $player) : void{
        if($this->hasFaction($player)){
            $player->sendMessage(TextFormat::DARK_GRAY . "> " . TextFormat::YELLOW . "Faction Info" . TextFormat::DARK_GRAY . " <");
            $player->sendMessage(TextFormat::BLUE . "Name: " . TextFormat::GRAY . $this->getFaction($player, "FName"));
            $player->sendMessage(TextFormat::BLUE . "Leader: " . TextFormat::GRAY . $this->getFaction($player, "FLeader"));
            $player->sendMessage(TextFormat::BLUE . "Members:");
            foreach($this->getFaction($player, "FMembers") as $members){
                $player->sendMessage(TextFormat::ITALIC . TextFormat::GRAY . "- " . TextFormat::YELLOW . $members);
            }
        }else{
            $player->sendMessage($this->translate("have-not-a-faction"));
        }
    }

    public function getOtherFactionInfo(Player $player, string $fname) : void {
        if($this->factionExist($fname)){
            $player->sendMessage(TextFormat::DARK_GRAY . "> " . TextFormat::YELLOW . "Faction Info" . TextFormat::DARK_GRAY . " <");
            $player->sendMessage(TextFormat::BLUE . "Name: " . TextFormat::GRAY . $this->getOtherFaction($fname, "FName"));
            $player->sendMessage(TextFormat::BLUE . "Leader: " . TextFormat::GRAY . $this->getOtherFaction($fname, "FLeader"));
            $player->sendMessage(TextFormat::BLUE . "Members:");
            foreach($this->getOtherFaction($fname, "FMembers") as $members){
                $player->sendMessage(TextFormat::ITALIC . TextFormat::GRAY . "- " . TextFormat::YELLOW . $members);
            }
        }else{
            $player->sendMessage($this->translate("faction-not-exist"));
        }
    }

    public function factionExist(string $fname) : bool{
        return file_exists($this->getDataFolder() . "factions/" . $fname . ".yml");
    }

    public function kickOutofFaction(Player $player, Player $who) : void{
        $this->setPFaction($who, null);
        //TODO Kick from member list $this->setFaction($player, "FMembers", "");
    }

    public function playerExist(string $name) : bool{
        return file_exists($this->getDataFolder() . "players/" . $name . ".yml");
    }

    public function setPFacNameTag(Player $player) : void{
        if($this->getConf("faction-nametag")){
            if(!empty($this->getPConf($player, "Faction"))){
                $player->setNameTag(TextFormat::DARK_PURPLE . $this->getFactionName($player) . TextFormat::GRAY . " : " . TextFormat::WHITE . $player->getName());
            }else{
                $player->setNameTag(TextFormat::WHITE . $player->getName());
            }
        }
    }

    public static function getInstance() : self{
        return self::$instance;
    }

    public function onDisable() : void{
        $this->getLogger()->info(TextFormat::RED . "FactionsPE " . self::VERSION . " Disabled");
    }
}