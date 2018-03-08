<?php

namespace FactionsPE\Events;

use FactionsPE\FactionsPE;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;

class DamageEvent extends EventListener {

    private $plugin;

    public function __construct(FactionsPE $plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onDamage(EntityDamageEvent $event){
        $entity = $event->getEntity();
        $lastdmg = $entity->getLastDamageCause();
        if ($lastdmg instanceof EntityDamageByEntityEvent){
            $damager = $lastdmg->getDamager();
            if ($entity instanceof Player and $damager instanceof Player){
                if ($this->plugin->isInSameFaction($damager, $entity)){
                    $event->setCancelled(true);
                    $damager->sendMessage($this->plugin->translate("cant-attack-fmember"));
                }
            }
        }
    }
}