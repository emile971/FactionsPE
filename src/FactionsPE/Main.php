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