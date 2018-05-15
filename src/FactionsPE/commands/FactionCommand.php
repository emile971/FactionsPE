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

namespace FactionsPE\commands;

use FactionsPE\FactionsPE;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FactionCommand extends PluginCommand{

    public function __construct($name, FactionsPE $plugin){
        parent::__construct($name, $plugin);
    }

    public function execute(CommandSender $sender, string $alias, array $args) : bool{
        if($sender instanceof Player) {
            if (isset($args[0])) {
                switch ($args[0]) {
                    case "create":
                        if (!FactionsPE::getInstance()->hasFaction($sender)) {
                            if (!empty($args[1])) {
                                if (strlen($args[1]) < FactionsPE::getInstance()->getConf("max-length") and strlen($args[1]) > FactionsPE::getInstance()->getConf("min-length")) {
                                    if (!FactionsPE::getInstance()->factionExist($args[1])) {
                                        FactionsPE::getInstance()->createFaction($args[1], $sender);
                                        FactionsPE::getInstance()->setPFaction($sender, $args[1]);
                                        $sender->sendMessage(FactionsPE::getInstance()->translate("faction-created"));
                                        FactionsPE::getInstance()->setPFacNameTag($sender);
                                    } else {
                                        $sender->sendMessage(FactionsPE::getInstance()->translate("faction-already-exists"));
                                    }
                                } else {
                                    $sender->sendMessage(FactionsPE::getInstance()->translate("faction-name-tolongorshort"));
                                    //TODO SEND MAX AND MIN.
                                }
                            }
                        } else {
                            $sender->sendMessage(FactionsPE::getInstance()->translate("has-already-a-faction"));
                        }
                        break;
                    case "delete":
                    case "del":
                        FactionsPE::getInstance()->deleteFaction($sender);
                        break;
                    case "invite":
                        if (isset($args[1])){
                            FactionsPE::getInstance()->inviteToFaction($args[1], FactionsPE::getInstance()->getFactionName($sender));
                        }
                        break;
                    case "kick":
                        if (!empty($args[1])) {
                            if (FactionsPE::getInstance()->hasFaction($sender)) {
                                if (FactionsPE::getInstance()->isFactionLeader($sender)) {
                                    if (FactionsPE::getInstance()->playerExist($args[1])) {
                                        $who = FactionsPE::getInstance()->getServer()->getPlayer($args[1]);
                                        if (FactionsPE::getInstance()->getFaction($sender, "FLeader") != $who->getName()) {
                                            $kicked = FactionsPE::getInstance()->translate("kicked-name-from-faction");
                                            $kickmsg = str_replace("{who}", $who->getName(), $kicked);
                                            FactionsPE::getInstance()->kickOutofFaction($sender, $who);
                                            $sender->sendMessage($kickmsg);
                                        } else {
                                            $sender->sendMessage(FactionsPE::getInstance()->translate("cant-kick-leader"));
                                        }
                                    } else {
                                        $sender->sendMessage(FactionsPE::getInstance()->translate("player-not-exist"));
                                    }
                                } else {
                                    $sender->sendMessage(FactionsPE::getInstance()->translate("not-faction-leader"));
                                }
                            }
                        }
                        break;
                    case "info":
                        if (isset($args[1])) {
                            FactionsPE::getInstance()->getOtherFactionInfo($sender, $args[1]);
                        } else {
                            FactionsPE::getInstance()->getFactionInfo($sender);
                        }
                        break;
                    case "help":
                    case "h":
                        $this->sendHelpList($sender);
                        break;
                    default:
                        $sender->sendMessage(FactionsPE::getInstance()->translate("command-usage"));
                        break;
                }
            } else {
                $sender->sendMessage(FactionsPE::getInstance()->translate("not-a-player"));
            }
        }
        return true;
    }

    private function sendHelpList(Player $player) : void{
        $player->sendMessage(TextFormat::YELLOW . "=| " . TextFormat::DARK_PURPLE . "Factions" . TextFormat::GREEN . " Help" . TextFormat::YELLOW . " |=");
        $player->sendMessage(TextFormat::GRAY . "- /f help (Shows this list)");
        $player->sendMessage(TextFormat::GRAY . "- /f create {fname} (Create a Faction)");
        $player->sendMessage(TextFormat::GRAY . "- /f delete (Delete our faction)");
        $player->sendMessage(TextFormat::GRAY . "- /f info [fname] (Get Faction Informations)");
        $player->sendMessage(TextFormat::GRAY . "- /f kick {player} (Kick anyone from our Faction)");
    }
}