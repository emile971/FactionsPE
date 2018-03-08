<?php

namespace FactionsPE\Events;

use FactionsPE\FactionsPE;
use pocketmine\event\Listener;

abstract class EventListener implements Listener {

    public function __construct(FactionsPE $plugin) {
    }
}