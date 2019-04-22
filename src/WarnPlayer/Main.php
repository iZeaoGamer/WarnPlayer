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
 if(!is_dir($this->getDataFolder() . $this->config->get("bans-data") . "/")) mkdir($this->getDataFolder() . $this->config->get("bans-data")); //To-do customise how bans database should generate.
 if(!is_file($this->getDataFolder() . "messages.yml")) { $this->saveResource("messages.yml");}
 $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML, array());

//public function ActionTypes(): void{ todo add a function for all action types.
              $action = $this->config->get("action_after_three_warns");
              if($action === "kick") {
                  $this->getLogger()->info("You chose the kick option. Enabling WarnPlayer.");
               }elseif($action === "ban"){
                  $this->getLogger()->info("You chose the ban option. Enabling WarnPlayer");
              }elseif($action === "message"){
                $this->getLoger()->info("You chose the message option. Enabling WarnPlayer.");
              }elseif($action === "ban-ip"){
$this->getLogger()->info("You chose the  Ban IP option. Enabling WarnPlayer.");
                //to-do add CID Ban as action type.
               }elseif($action === null){
                $this->getLogger()->error($action . " in file config.yml is invalid, valid options: kick, ban, ban-ip, and message. Disabling plugin.");
                $this->getServer()->getPluginManager()->disablePlugin($this->getServer()->getPluginManager()->getPlugin("WarnPlayer"));
                return true;
        }
  }
    //public function onConfigGeneration(); //to-do add function for config generation onEnable() state.
    
    //public function onMessageGeneration(); //to-do add function for messages.yml generation onEnable() state.
    
    //public function generatePlayerData(Player $player): void{ to-do add a function for generating playerdata.
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
   $maxWarns = $this->config->get("max-warns");
    if($player->isBanned()){
         $player->close("", TF::colorize(str_replace(["{maxwarns}"], [$maxwarns], $this->messages->get("ban_message"))));
    }
}
/*
public function BansDataBase(Config $config): void{ //To-Do implement public functions.
  $this->config->get($config);
}*/
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
      if(strtolower($cmd->getName()) === "warn") { 
        if(!$sender->hasPermission("playerwarn.warn")){
              $sender->sendMessage(TF::colorize($this->messages->get("warns-no-permission")));
              return true;
          }
        if(!(isset($args[0]))){
          $sender->sendMessage(TF::colorize($this->messages->get("no-arguments")));
          return true;
        } else {
          $sender_name = $sender->getName();
          $name = $args[0];
          $player = $this->getServer()->getPlayer($name);
          if($player === null) {
            $sender->sendMessage(TF::colorize(str_replace(["{player}"], [$name], $this->messages->get("player-not-found"))));
            return true;
          } else {
            unset($args[0]);
            $player_name = $player->getName();
            if(!(file_exists($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt"))) {
              touch($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt");
              file_put_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt", "0");
            }
if($this->config->get("require-reason") === true){
            if(empty($args[1])){
$sender->sendMessage(TF::colorize($this->messages->get("valid-reason")));
return true;
}
}
}
          $reason = implode(" ", $args);
            $file = file_get_contents($this->getDataFolder() . $this->config->get("player_data") . "/" . $player_name . ".txt");
            $maxWarns = $this->config->get("max-warns");
            if($file >= $maxWarns) {
              $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
              $action = $this->config->get("action_after_three_warns");
              if($action === "kick") {
                if($this->config->get("enable-ops-status") === true){
                if($player->isOP())
                    $sender->sendMessage(TF::colorize($this->messages->get("cannot-kick-ops")));
                  return true;
                }
              }
                $maxwarns = $this->config->get("max-warns");
                $player->kick(TF::colorize(str_replace(["{maxwarns}"], [$maxwarns], $this->messages->get("kick_message"))));
                $sender->sendMessage(TF::colorize(str_replace(["{player}", "{maxwarns}"], [$player->getName(), $maxwarns], $this->messages->get("kick-sender-message"))));
                return true;
              } else if($action === "ban") {
                if($this->config->get("enable-ops-status") === true){
                if($player->isOP())
                    $sender->sendMessage(TF::colorize($this->messages->get("cannot-ban-ops")));
                  return true;
                }
               $banList = $sender->getServer()->getNameBans();
                $maxwarns = $this->config->get("max-warns");
               $banList->addBan($player_name, $reason, null, $sender->getName());
                $player->kick(TF::colorize(str_replace(["{maxwarns}"], [$maxwarns], $this->messages->get("ban_message"))));
                if(!(file_exists($this->getDataFolder() . $this->config->get("bans-data") . "/" . $player_name))){
                    touch($this->getDataFolder() . $this->config->get("bans-data") . "/" . $player_name . ".txt");
                    file_put_contents($this->getDataFolder() . $this->config->get("bans-data") . "/" . $player_name . ".txt", "Banned player: $player_name\nReason: $reason"); //to-do customize what goes in the bans database / file.
                $sender->sendMessage(TF::colorize(str_replace(["{player}", "{maxwarns}"], [$player->getName(), $maxwarns], $this->messages->get("ban-sender-message"))));
                return true;
                }elseif($action === "ban-ip"){
                  $banList = $sender->getServer()->getIPBans();
                  $banlist->addBan($player->getAddress(), $reason, null, $sender->getName());
                  $player->kick(TF::colorize(str_replace(["{sender_name}", "{maxwarns}"], [$sender->getName(), $maxwarns], $this->messages->get("ban-ip-message"))));
                  if(!(file_exists($this->getDataFolder() . $this->config->get("bans-data") . "/" . $player_name))){
                    touch($this->getDataFolder() . $this->config->get("bans-data") . "/" . $player_name . ".txt");
                    file_put_contents($this->getDataFolder() . $this->config->get("bans-data") . "/" . $player_name . ".txt", "Banned player: $player_name\nBan type: $action\nReason: $reason");
                }elseif($action === "message"){
                  $player->sendMessage(TF::colorize(str_replace(["{sender_name}", "{maxwarns}"], [$sender->getName(), $maxwarns], $this->messages->get("message-action-text"))));
              } elseif($action === null){
                $this->getLogger()->error($action . " in file config.yml is invalid, valid options: kick, ban, ban-ip, message. Disabling plugin.");
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
        if(!$sender->hasPermission("playerwarn.warns")){
              $sender->sendMessage(TF::colorize($this->messages->get("warns-no-permission")));
              return true;
          }
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
