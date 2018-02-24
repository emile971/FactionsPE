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

use pocketmine\command\{CommandSender, PluginCommand};
use pocketmine\Player;
use pocketmine\utils\TextFormat as C;
use FactionsPE\Main;

class FactionCommand extends PluginCommand {

    private $plugin;

	public function __construct($name, Main $plugin){
	    $this->plugin = $plugin;
		parent::__construct($name, $plugin);
		$this->setDescription("Shows faction commands.");
	}

	public function execute(CommandSender $sender, string $alias, array $args){
	    if ($sender instanceof Player) {
            if (count($args) < 1) {
                $sender->sendMessage($this->plugin->translate("command-usage"));
            }
            switch ($args[0]) {
                case "test":
                    $sender->sendMessage(C::YELLOW . "Only a Test");
                    break;
                case "create":
                    break;
                case "help":
                    $this->sendHelpList($sender);
                    break;
                default:
                    $sender->sendMessage($this->plugin->translate("command-usage"));
            }
        }
	}

	private function sendHelpList(Player $player){
	    $player->sendMessage(C::YELLOW."=> ".C::DARK_PURPLE."Factions".C::GREEN." Help".C::YELLOW." <=");
	    $player->sendMessage(C::GRAY."- /f test");
        $player->sendMessage(C::GRAY."- /f help");
        $player->sendMessage(C::GRAY."- /f create");
    }
}