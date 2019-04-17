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
  public function onEnable() {
      if(!is_file($this->getDataFolder() . "config.yml")) { $this->saveDefaultConfig(); }
      $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
  $this->getServer()->getPluginManager()->registerEvents($this, $this);
 if (!is_dir($this->getDataFolder())) { @mkdir($this->getDataFolder()); }
 if (!is_dir($this->getDataFolder() . $this->config->get("player_data") . "/")) { @mkdir($this->getDataFolder() . $this->config->get("player_data")); }
 if(!is_dir($this->getDataFolder() . "Bans/")) mkdir($this->getDataFolder() . "Bans");
 if(!is_file($this->getDataFolder() . "messages.yml")) { $this->saveResource("messages.yml");}
 $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML, array());
 $string = "action_after_three_warns: ";
              $action = $this->config->get($string);

              if($action === "kick") {
                  $this->getLogger()->info("You chose the kick option. Enabling WarnPlayer.");
              } elseif($action === "ban"){
                  $this->getLogger()->info("You chose the ban option. Enabling WarnPlayer");
              } elseif($action === null){
                $this->getLogger()->error($action . " in file config.yml is invalid, valid options: kick, ban. Disabling plugin.");
                $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("WarnPlayer"));
                return true;
        }
    }
    public function onJoin(PlayerJoinEvent $event){
      $player = $event->getPlayer();
      $this->config = new Config($this->getDataFolder() . "config.yml", CONFIG::YAML, array());
       if($this->config->get("generate-player-data-on-join") === true){
       }
if(!(file_exists($this->getDataFolder() . $this->config->get("player_data") . "/" . $player->getName() . ".txt"))) {
  $this->getLogger()->info(str_replace(["{player}"], [$player->getName()], $this->messages->get("generate-data-message")));
              touch($this->getDataFolder() . $this->config->get("player_data") . "/" . $player->getName() . ".txt");
              file_put_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player->getName() . ".txt", 0);
            }
}
public function onPlayerBan(PlayerPreLoginEvent $event){
    $player = $event->getPlayer();
    if($player->isBanned()){
        //if(!(file_exists($this->getDataFolder() . "Bans/") . $player->getName())){
         //   touch($this->getDataFolder() . "Bans/" . $player->getName() . ".txt");
           // file_put_contents($this->getDataFolder() . "Bans/" . $player->getName() . ".txt", "banned player: " . $player->getName()); //to-do add reason to the ban database
        $player->close("", TF::colorize($this->messages->get("ban_message")));
    }
}
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
      if(strtolower($cmd->getName()) === "warn") { 
        if(!(isset($args[0]))){
          $sender->sendMessage(TF::colorize($this->messages->get("no-arguments")));
          return true;
        } else {
          $sender_name = $sender->getName();
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
          if($player === null) {
            $sender->sendMessage(TF::colorize($this->messages->get("player-not-found"))); //To-Do customise it to show variables. 
            return true;
          } else {
            unset($args[0]);
            $player_name = $player->getName();
            if(!(file_exists($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt"))) {
              touch($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt");
              file_put_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt", "0"); //To-Do customise playerdata generation (edit where the target should go to.)
            }
if($this->config->get("require-reason") === true){
            if(empty($args[1])){
$sender->sendMessage(TF::colorize($this->messages->get("valid-reason")));
return true;
}
}
          $reason = implode(" ", $args);
            $file = file_get_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt");
            $maxWarns = $this->config->get("max-warns");
            if($file >= $maxWarns) {
              $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
              $action = $this->config->get("action_after_three_warns");
              if($action === "kick") {
                if($player->isOP()){
                    $sender->sendMessage(TF::colorize($this->messages->get("cannot-kick-ops")));
                  return true;
                }
                $maxwarns = $this->config->get("max-warns");
                $player->kick(TF::colorize(str_replace(["{maxwarns}"], [$maxwarns], $this->config->get("kick_message"))));
                $sender->sendMessage(TF::colorize(str_replace(["{player}", "{maxwarns}"], [$player->getName(), $maxwarns], $this->messages->get("kick-message"))));
                return true;
              } else if($action === "ban") {
                if($player->isOP()){
                    $sender->sendMessage(TF::colorize($this->messages->get("cannot-ban-ops")));
                  return true;
                }
               $banList = $sender->getServer()->getNameBans();
                $maxwarns = $this->config->get("max-warns");
               $banList->addBan($player_name, $reason, null, $sender->getName());
                $player->kick(TF::colorize(str_replace(["{maxwarns}"], [$maxwarns], $this->config->get("ban_message"))));
                if(!(file_exists($this->getDataFolder() . "Bans/" . $player_name))){
                    touch($this->getDataFolder() . "Bans/" . $player_name . ".txt");
                    file_put_contents($this->getDataFolder() . "Bans/" . $player_name . ".txt", "Banned player: $player_name\nReason: $reason");
                }
                $sender->sendMessage(TF::colorize(str_replace(["{player}", "{maxwarns}"], [$player->getName(), $maxwarns], $this->messages->get("ban-message"))));
                return true;
              } elseif($action === null){
                $this->getLogger()->error($action . " in file config.yml is invalid, valid options: kick, ban. Disabling plugin.");
                $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("WarnPlayer"));
                return true;
              }
            } else {
              $player->sendMessage(TF::colorize(str_replace(["{sender_name}", "{reason}"], [$sender_name, $reason], $this->messages->get("warned-message"))));
              $file = file_get_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt");
              file_put_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt", $file + 1);
              $sender->sendMessage(TF::colorize(str_replace(["{player}"], [$player_name], $this->messages->get("added-warn-message"))));
              if($this->config->get("enable-warnbroadcast-message") === true)
                $this->getServer()->broadcastMessage(TF::colorize(str_replace(["{sender_name}", "{reason}"], [$sender_name, $reason], $this->messages->get("warn-broadcast-message"))));
              return true;
            }
          }
        }
        }
      if(strtolower($cmd->getName()) === "warns") {
        if(!(isset($args[0]))){
            $sender->sendMessage(TF::colorize($this->messages->get("warns-usage")));
          return true;
        } else {
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
          if($player === null){
            $sender->sendMessage(TF::colorize(str_replace(["{player}"], [$name], $this->messages->get("player-not-found"))));
            return true;
          } else {
            $player_name = $player->getName();
            if(!(file_exists($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt"))) {
              $sender->sendMessage(TF::colorize(str_replace(["{player}"], [$player_name], $this->messages->get("no-warns"))));
              return true;
            } else {
              $player_warns = file_get_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt");
              $sender->sendMessage(TF::colorize(str_replace(["{player}", "{warns}"], [$player_name, $player_warns], $this->messages->get("player-warns"))));
              return true;
            }
          }
        }
    return true;
  }
  }
  }
