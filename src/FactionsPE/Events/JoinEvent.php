<?php

namespace FactionsPE\Events;

use FactionsPE\FactionsPE;
use pocketmine\event\player\PlayerJoinEvent;

class JoinEvent extends EventListener {

    private $plugin;

    public function __construct(FactionsPE $plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $pname = $player->getName();
        if (!file_exists($this->plugin->getDataFolder()."players/".$pname.".yml")){
            $this->plugin->initPConfig($player);
        }
    }
}