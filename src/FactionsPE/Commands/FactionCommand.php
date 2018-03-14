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

namespace FactionsPE\Commands;

use FactionsPE\FactionsPE;
use pocketmine\command\{
    CommandSender, PluginCommand
};
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class FactionCommand extends PluginCommand{

    private $plugin;

    public function __construct($name, FactionsPE $plugin){
        $this->plugin = $plugin;
        parent::__construct($name, $plugin);
    }

    public function execute(CommandSender $sender, string $alias, array $args) : bool{
        if($sender instanceof Player){
            if(isset($args[0])){
                switch($args[0]){
                    case "create":
                        if(!$this->plugin->hasFaction($sender)){
                            if(!empty($args[1])){
                                if(strlen($args[1]) < $this->plugin->getConf("max-length") and strlen($args[1]) > $this->plugin->getConf("min-length")){
                                    if(!$this->plugin->factionExist($args[1])){
                                        $this->plugin->createFaction($args[1], $sender);
                                        $this->plugin->setPFaction($sender, $args[1]);
                                        $sender->sendMessage($this->plugin->translate("faction-created"));
                                        $this->plugin->setPFacNameTag($sender);
                                    }else{
                                        $sender->sendMessage($this->plugin->translate("faction-already-exists"));
                                        return false;
                                    }
                                }else{
                                    $sender->sendMessage($this->plugin->translate("faction-name-tolongorshort"));
                                    return false;
                                    //TODO SEND MAX AND MIN.
                                }
                            }
                        }else{
                            $sender->sendMessage($this->plugin->translate("has-already-a-faction"));
                            return false;
                        }
                        break;
                    case "delete":
                    case "del":
                        $this->plugin->deleteFaction($sender);
                        break;
                    case "invite":
                        break;
                    case "kick":
                        if(!empty($args[1])){
                            if($this->plugin->hasFaction($sender)){
                                if($this->plugin->isFactionLeader($sender)){
                                    if($this->plugin->playerExist($args[1])){
                                        $who = $this->plugin->getServer()->getPlayer($args[1]);
                                        if($this->plugin->getFaction($sender, "FLeader") != $who->getName()){
                                            $kicked = $this->plugin->translate("kicked-name-from-faction");
                                            $kickmsg = str_replace("{who}", $who->getName(), $kicked);
                                            $this->plugin->kickOutofFaction($sender, $who);
                                            $sender->sendMessage($kickmsg); //TODO Returns "" idk why. -.-
                                        }else{
                                            $sender->sendMessage($this->plugin->translate("cant-kick-leader"));
                                            return false;
                                        }
                                    }else{
                                        $sender->sendMessage($this->plugin->translate("player-not-exist"));
                                        return false;
                                    }
                                }else{
                                    $sender->sendMessage($this->plugin->translate("not-faction-leader"));
                                    return false;
                                }
                            }
                        }
                        break;
                    case "info":
                        if(empty($args[1])){
                            $this->plugin->getFactionInfo($sender);
                        }else{
                            $this->plugin->getOtherFactionInfo($sender, $args[1]);
                        }
                        break;
                    case "help":
                    case "h":
                        $this->sendHelpList($sender);
                        break;
                    default:
                        $sender->sendMessage($this->plugin->translate("command-usage"));
                }
            }
        }else{
            $sender->sendMessage($this->plugin->translate("not-a-player"));
            return false;
        }
        return true;
    }

    private function sendHelpList(Player $player) : void{
        $player->sendMessage(C::YELLOW . "=| " . C::DARK_PURPLE . "Factions" . C::GREEN . " Help" . C::YELLOW . " |=");
        $player->sendMessage(C::GRAY . "- /f help (Shows this list)");
        $player->sendMessage(C::GRAY . "- /f create {fname} (Create a Faction)");
        $player->sendMessage(C::GRAY . "- /f delete (Delete our faction)");
        $player->sendMessage(C::GRAY . "- /f info [fname] (Get Faction Informations)");
        $player->sendMessage(C::GRAY . "- /f kick {player} (Kick anyone from our Faction)");
    }
}