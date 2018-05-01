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

namespace FactionsPE\events;

use FactionsPE\FactionsPE;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class DamageEventListener implements Listener{

    public function onDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        $lastdmg = $entity->getLastDamageCause();
        if($lastdmg instanceof EntityDamageByEntityEvent){
            $damager = $lastdmg->getDamager();
            if($entity instanceof Player and $damager instanceof Player){
                if(FactionsPE::getInstance()->getConf("friendly-attack") != true){
                    if(FactionsPE::getInstance()->isInSameFaction($damager, $entity)){
                        $event->setCancelled(true);
                        $damager->sendMessage(FactionsPE::getInstance()->translate("cant-attack-fmember"));
                    }
                }
            }
        }
    }
}