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

namespace FactionsPE\Commands;

use FactionsPE\FactionsPE;
use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;

class FactionCommand extends PluginCommand {

    private $plugin;

	public function __construct($name, FactionsPE $plugin){
	    $this->plugin = $plugin;
        $this->setDescription("Shows faction commands.");
        parent::__construct($name, $plugin);
	}

	public function execute(CommandSender $sender, string $alias, array $args){
	    if ($sender instanceof Player) {
            if (empty($args)) {
                $sender->sendMessage($this->plugin->translate("command-usage"));
            }
            switch ($args[0]) {
                case "create":
                    if (strlen($args[1]) < $this->plugin->getConf("max-lenght")) {
                        if (!file_exists($this->plugin->getDataFolder()."factions/".$args[1].".yml")) {
                            $this->plugin->createFaction($args[1], $sender);
                            $this->plugin->setPFaction($sender, $args[1]);
                        }else{
                            $sender->sendMessage($this->plugin->translate("faction-already-exists"));
                        }
                    }else{
                        $sender->sendMessage($this->plugin->translate("faction-name-toolong"));
                    }
                    break;
                case "delete":
                case "del":
                    $this->plugin->deleteFaction($sender);
                    break;
                case "help":
                case "h":
                    $this->sendHelpList($sender);
                    break;
                default:
                    $sender->sendMessage($this->plugin->translate("command-usage"));
            }
        }else{
	        $sender->sendMessage($this->plugin->translate("not-a-player"));
        }
	}

	private function sendHelpList(Player $player) : void{
	    $player->sendMessage(C::YELLOW."=| ".C::DARK_PURPLE."Factions".C::GREEN." Help".C::YELLOW." |=");
        $player->sendMessage(C::GRAY."- /f help (Shows this list)");
        $player->sendMessage(C::GRAY."- /f create {name} (Create a Faction)");
        $player->sendMessage(C::GRAY."- /f delete {Deletes the faction}");
    }
}