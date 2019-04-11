<?php

  namespace WarnPlayer;
  use pocketmine\plugin\PluginBase;
  use pocketmine\event\Listener;
  use pocketmine\utils\TextFormat as TF;
  use pocketmine\Player;
  use pocketmine\command\Command;
  use pocketmine\command\CommandSender;
  use pocketmine\utils\Config;
  use pocketmine\event\player\PlayerJoinEvent;
  use pocketmine\event\player\PlayerPreLoginEvent;
  class Main extends PluginBase implements Listener {
    public function dataPath() {
      return $this->getDataFolder();
  }
  public function onEnable() {
  $this->getServer()->getPluginManager()->registerEvents($this, $this);
 if (!is_dir($this->getDataFolder())) { @mkdir($this->getDataFolder()); }
 if (!is_dir($this->getDataFolder() . "Players/")) { @mkdir($this->getDataFolder() . "Players"); }
 if (!is_file($this->getDataFolder() . "config.yml")) { $this->saveDefaultConfig(); }
 $this->config = new Config($this->dataPath() . "config.yml", Config::YAML, array());
    $this->config->set("action_after_three_warns: ", "kick");
        $this->config->set("generate-player-data-on-join", true);
        $this->config->set("require-reason", true);
        $this->config->set("kick_reason", "&aYou have been kicked for being warned 3+ times.");
 $string = "action_after_three_warns: ";
              $this->config = new Config($this->dataPath() . "config.yml", Config::YAML, array());
              $action = $this->config->get($string);

              if($action === "kick") {
                  $this->getLogger()->info("You chose the kick option. Enabling WarnPlayer.");
              } elseif($action === "ban"){
                  $this->getLogger()->info("You chose the ban option. Enabling WarnPlayer");
        }
    }
    public function onJoin(PlayerJoinEvent $event){
      $player = $event->getPlayer();
      $this->config = new Config($this->dataPath() . "config.yml", CONFIG::YAML, array());
        $this->config->set("action_after_three_warns: ", "kick");
        $this->config->set("generate-player-data-on-join", true);
        $this->config->set("require-reason", true);
       if($this->config->get("generate-player-data-on-join") === true){
}
if(!(file_exists($this->dataPath() . "Players/" . $player->getName() . ".txt"))) {
              touch($this->dataPath() . "Players/" . $player->getName() . ".txt");
              file_put_contents($this->dataPath() . "Players/" . $player->getName() . ".txt", 0);
            }
}
public function onPlayerBan(PlayerPreLoginEvent $event){
    $player = $event->getPlayer();
    if($player->isBanned()){
        $player->close("", TF::colorize($this->config->get("ban_message")));
    }
}
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
      if(strtolower($cmd->getName()) === "warn") {
        if(!(isset($args[0]))){
          $sender->sendMessage(TF::colorize("&6Error: not enough args. Usage: &b/warn <player> <reason>"));
          return true;
        } else {
          $sender_name = $sender->getName();
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
          if($player === null) {
            $sender->sendMessage(TF::colorize("&cPlayer &4" . $name . " &ccould not be found."));
            return true;
          } else {
            unset($args[0]);
            $player_name = $player->getName();
            if(!(file_exists($this->dataPath() . "Players/" . $player_name . ".txt"))) {
              touch($this->dataPath() . "Players/" . $player_name . ".txt");
              file_put_contents($this->dataPath() . "Players/" . $player_name . ".txt", "0");
            }
if($this->config->get("require-reason") === true){
            if(empty($args[1])){
$sender->sendMessage(TF::colorize("&cYou need to enter a valid reason."));
return true;
}
}
            $reason = implode(" ", $args);
            $file = file_get_contents($this->dataPath() . "Players/" . $player_name . ".txt");
            if($file >= "3") { //To do make this configurable.
              $this->config = new Config($this->dataPath() . "config.yml", Config::YAML, array());
              $action = $this->config->get("action_after_three_warns");
              if($action === "kick") {
                if($player->isOP()){
                  return true;
                }
                $player->kick(TF::colorize($this->config->get("kick_message")));
                $sender->sendMessage(TF::colorize("&b" . $player_name . " &6was kicked for being warned 3+ times."));
                return true;
              } else if($action === "ban") {
                if($player->isOP()){
                  return true;
                }
               $banList = $sender->getServer()->getNameBans();
               $banList->addBan($player_name, $reason, null, $sender->getName());
                $player->kick(TF::colorize($this->config->get("ban_message")));
                $sender->sendMessage(TF::colorize("&b" . $player_name . " &6was banned for being warned 3+ times."));
                return true;
            } else {
              $player->sendMessage(TF::colorize("&6You have been warned by &b" . $sender_name . " &6for &b" . $reason));
              $this->getServer()->broadcastMessage(TF::colorize("&b" . $player_name . " &6was warned by &b" . $sender_name . " &6for &b" . $reason));
              $file = file_get_contents($this->dataPath() . "Players/" . $player_name . ".txt");
              file_put_contents($this->dataPath() . "Players/" . $player_name . ".txt", $file + 1);
              $sender->sendMessage(TF::colorize("&6Warned &b" . $player_name . ", &6and added +1 warns to their file."));
              return true;
            }
          }
        }
        }
      }
      if(strtolower($cmd->getName()) === "warns") {
        if(!(isset($args[0]))) {
          $sender->sendMessage(TF::colorize("&cError: not enough args. Usage: /warns <player>"));
          return true;
        } else {
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
          if($player === null) {
            $sender->sendMessage(TF::colorize("&cPlayer &4" . $name . " &ccould not be found."));
            return true;
          } else {
            $player_name = $player->getName();
            if(!(file_exists($this->dataPath() . "Players/" . $player_name . ".txt"))) {
              $sender->sendMessage(TF::colorize("&4" . $player_name . " &chas no warns."));
              return true;
            } else {
              $player_warns = file_get_contents($this->dataPath() . "Players/" . $player_name . ".txt");
              $sender->sendMessage(TF::colorize("&6Player &b" . $player_name . " &6has &b" . $player_warns . " &6warns."));
              return true;
            }
          }
        }
return true;
      }
      }
  }
