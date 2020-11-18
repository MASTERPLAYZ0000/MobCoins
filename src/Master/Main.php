<?php

namespace Master;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Config;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Zombie;

class Main extends PluginBase implements Listener {

   public $cfg;

   public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->cfg = $this->getConfig();
        $this->cfg->save();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);  
        $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
        $coinconfig->save();
        $this->getLogger()->info("MobCoins Loaded Successfully");
   }
   
   public function onDisable() {
        $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
        $coinconfig->save();
   }
   
   public function onJoin(PlayerJoinEvent $event) : void {
        $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML, [$event->getPlayer()->getName() => $this->cfg->get("start")]);
        $player = $event->getPlayer();
        $coins = $coinconfig->getAll();
        arsort($coins);
    }
    
    public function addCoin(Player $player, int $amount) : void { 
        $player->sendTip(TF::GREEN . "You got +" . $amount . " MobCoins");
        $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
        $coinconfig->set($player->getName(), $coinconfig->get($player->getName())+ $amount);
        $coinconfig->save();
    }
    
    public function takeCoin(Player $player, int $amount) : void { 
        $player->sendTip(TF::RED . "You lost -" . $amount . " MobCoins");
        $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
        $coinconfig->set($player->getName(), $coinconfig->get($player->getName())- $amount);
        $coinconfig->save();
    }
    
    public function getCoins(Player $player) {
        $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
        return $coinconfig->get($player->getName());
    }

    public function onZombieDeath(EntityDeathEvent $event) {
        $entity = $event->getEntity();
        if (!$entity instanceof Zombie) return;
        $cause = $entity->getLastDamageCause();
        if (!$cause instanceof EntityDamageByEntityEvent) return;
        $damager = $cause->getDamager();
        if ($damager instanceof Player) $reward = rand(1, 10);
        switch($reward) {
            case 1:
            break;
            case 2:
            break;
            case 3:
            $this->addCoin($damager, $this->cfg->get("zombie_coin"));
            break;
            case 4:
            break;
            case 5:
            break;
            case 6:
            break;
            case 7:
            $this->addCoin($damager, $this->cfg->get("zombie_coin"));
            break;
            case 8:
            break;
            case 9:
            break;
            case 10:
            $this->addCoin($damager, $this->cfg->get("zombie_coin"));
            break;
            }
    }  
    
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {
            case "mc":
                if ($sender instanceof Player) {
                    if (empty($args[0])) {
                        $sender->sendMessage(TF::RED . "/mc help");
                        return true;
                    }
                    switch ($args[0]) {
                        case "give":
          
                            if(!$sender->hasPermission("mc.give")){
                                $sender->sendMessage(TF::RED . "You dont have permission to use this command");
                                return true;
                            }
                            if (empty($args[1])) {
                                $sender->sendMessage(TF::RED . "/mc give <player> <amount>");
                                return true;
                            }
                            $player = $this->getServer()->getPlayer($args[1]);
                            if ($player === null) {
                                $sender->sendMessage(TF::RED . "The player mentioned is offline");
                                return true;
                            }
                            $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
                            $coinconfig->getAll();
                            if (empty($args[2])) {
                                $sender->sendMessage(TF::RED . "/mc give <player> <amount>");
                                return true;
                            }
                            if (!empty($args[2])) {
                                $count = $args[2] ?? 1;
                                $p = $player->getName();
                                $s = $sender->getName();
                                $coinconfig->set($player->getName(), $coinconfig->get($player->getName())+ $count);
                                $coinconfig->save();
                                $sender->sendTip(TF::GOLD . "Added " .  $count . " Mob Coins To " . $p);
                                $player->sendTip(TF::GREEN . "You Got " . $count . " Mob Coins From A Admin " . $s); 
                                    
                            }
                        }
                    
                        switch ($args[0]) {
                            case "take":
                               
                                if(!$sender->hasPermission("mc.take")){
                                    $sender->sendMessage(TF::RED . "You dont have permission to use this command");
                                    return true;
                                }
                                if (empty($args[1])) {
                                    $sender->sendMessage(TF::RED . "/mc take <player> <amount>");
                                    return true;
                                }
                                $player = $this->getServer()->getPlayer($args[1]);
                                if ($player === null) {
                                    $sender->sendMessage(TF::RED . "The player mentioned is offline");
                                    return true;
                                }
                                $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
                                $coinconfig->getAll();
                                if (empty($args[2])) {
                                $sender->sendMessage(TF::RED . "/mc take <player> <amount>");
                                  return true;
                                }
                                if (!empty($args[2])) {
                                   $count = $args[2] ?? 1;
                                   $p = $player->getName();
                                   $s = $sender->getName();
                                   $coinconfig->set($player->getName(), $coinconfig->get($player->getName())- $count);
                                   $coinconfig->save();
                                   $player->sendTip(TF::RED . $count . " Mob Coins Was Taken By A Admin " . $s);
                                   $sender->sendTip(TF::GOLD . "Took " . $count . " Mob Coins From " . $p);
                                } 
                            }
                        switch ($args[0]) {
                            case "coins":
                            
                                if (empty($args[1])) {
                                    $cs = $this->getCoins($sender);
                                    $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
                                    $coinconfig->getAll();
                                    $sender->sendMessage(TF::GREEN . "You Have " . $cs . " Mob Coins");
                                    return true;
                                }
                                    $player = $this->getServer()->getPlayer($args[1]);
                                if ($player === null) {
                                    $sender->sendMessage(TF::RED . "The player mentioned is offline");
                                    return true;
                                }
                                    $cp = $this->getCoins($player);
                                    $p = $player->getName();
                                    $coinconfig = new Config($this->getDataFolder()."coins.yml", Config::YAML);
                                    $coinconfig->getAll();
                                    $sender->sendMessage(TF::GREEN . $p . " Has " . $cp . " Mob Coins");
                                }
                            
                        switch ($args[0]) {
                            case "help":
                                        $sender->sendMessage(TF::GREEN . "[MobCoins] Usage: /mc help");
                                        $sender->sendMessage(TF::GREEN . "[MobCoins] Usage: /mc give [Admin]");
                                        $sender->sendMessage(TF::GREEN . "[MobCoins] Usage: /mc take [Admin]");
                                        $sender->sendMessage(TF::GREEN . "[MobCoins] Usage: /mc coins");
                                    }
                    }
                    return true;
            }
      }
}
