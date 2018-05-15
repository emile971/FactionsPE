<?php

namespace FactionsPE;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        if(!$player->hasPlayedBefore()) FactionsPE::getInstance()->initPConfig($player);
        FactionsPE::getInstance()->setPFacNameTag($player);
    }

    public function onChat(PlayerChatEvent $event) : void{
        if (FactionsPE::getInstance()->getConf("faction-chattag") == true){
            $event->setFormat(FactionsPE::getInstance()->getFactionName($event->getPlayer()).": ".$event->getPlayer()->getName());
        }
    }

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