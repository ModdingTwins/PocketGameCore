<?php

namespace Core;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\lang\BaseLang;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\entity\Entity;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\utils\Textformat as Color;

class Main extends PluginBase implements Listener {

    public $prefix = "§l§ePocket§aGame§8 »§r";
    public $hideall = [];

    public function onEnable () {
		
		$prefix = new Config($this->getDataFolder() . "prefix.yml", Config::YAML);
            if(empty($prefix->get("Prefix"))) {
                $prefix->set("Prefix", "§l§ePocket§aGame§8 »§r");
			}
			$prefix->save();

        $this->saveResource("config.yml");
        @mkdir($this->getDataFolder());
        $this->prefix = $prefix->get("Prefix");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getLogger()->info("§4--------------------------------");
        $this->getServer()->getLogger()->info("§l§ePocket§aGame§8 » §aPlugin Enabled");
        $this->getServer()->getLogger()->info("§5Plugin by ModdingTwinz");
        $this->getServer()->getLogger()->info("§4--------------------------------");
		
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
            if(empty($config->get("JoinBroadcast"))) {
                $config->set("JoinBroadcast1", "§7=======================");
                $config->set("LEER", "");
                $config->set("JoinBroadcast2", " §l§6» §6Welcome to §ePocket§aGame");
                $config->set("JoinBroadcast3", " §l§6» §eServer Shop:");
                $config->set("JoinBroadcast4", " §l§6» §9Discord §aComming Soon");
                $config->set("LEER2", "");
                $config->set("JoinBroadcast5", "§7=======================");
                $config->set("BlockBreakMessage", " §e§cYou cannot break server areas");
                $config->set("Hub/Lobby", " §cWelocme");
                $config->set("JoinTitle", " §l§eWelcome {player}");
                $config->set("Prefix", "§l§ePocket§aGame§8 »");
        }
        $config->save();

        $info = new Config($this->getDataFolder() . "info.yml", Config::YAML);
        if(empty($info->get("infoline1"))){
            $info->set("infoline1", "§a===§7§l§ePocket§aGame§r§a===");
            $info->set("infoline2", "§l§6» §aDiscord Server:");
            $info->set("infoline3", "§l§6» §aCommingSoon");
            $info->set("infoline4", "§l§6» §aServer Shop:");
            $info->set("infoline5", "§a====================");
            $info->set("Popup", "§l[§ePocket§aGame§8 »§r");
        }
        $info->save();

        $LobbyTitle = new Config($this->getDataFolder() . "Title.yml", Config::YAML);
        if(empty($LobbyTitle->get("LobbySendigBackTitle"))){
            $LobbyTitle->set("LobbySendigBackTitle", " §l§ePocket§aGame");
        }
        $LobbyTitle->save();


    }
    public function onJoin(PlayerJoinEvent $ev) {
		
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $player = $ev->getPlayer();
        $name = $player->getName();
        $player->getInventory()->clearAll();
        $ev->setJoinMessage("§f[§b+§f]" . Color::AQUA . $name);
        $player->setFood(20);
        $player->setHealth(20);
        $player->setGamemode(2);
        $player->getlevel()->addSound(new AnvilUseSound($player));
        $player->sendPopup("§8× §eWelcome " . Color::AQUA . $player->getDisplayName() . Color::DARK_GRAY . " ×");
        $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
        $player->sendMessage($config->get("JoinBroadcast1"));
        $player->sendMessage($config->get("LEER"));
        $player->sendMessage($config->get("JoinBroadcast2"));
        $player->sendMessage($config->get("JoinBroadcast3"));
        $player->sendMessage($config->get("JoinBroadcast4"));
        $player->sendMessage($config->get("LEER2"));
        $player->sendMessage($config->get("JoinBroadcast5"));

        $player->getInventory()->setSize(9);
        $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
        $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
        $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
        $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
        $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
        if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));

        }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
        }
        $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));

    }

    public function onBreak(BlockBreakEvent $ev) {
		
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        $player = $ev->getPlayer();
        $ev->setCancelled(false);
        $player->sendMessage($this->prefix . $config->get("BlockBreakMessage"));

    }

    public function onQuit(PlayerQuitEvent $ev) {

        $player = $ev->getPlayer();
        $name = $player->getName();

        $ev->setQuitMessage("");
        $player->sendPopup("§f[§c-§f] ". Color:: AQUA . $name);
    }

    public function onPlace(BlockPlaceEvent $ev) {

        $player = $ev->getPlayer();
        $ev->setCancelled(false);

    }

    public function Hunger(PlayerExhaustEvent $ev) {

        $ev->setCancelled(true);

    }

    public function ItemMove(PlayerDropItemEvent $ev){

        $ev->setCancelled(true);
    }

    public function onConsume(PlayerItemConsumeEvent $ev){

        $ev->setCancelled(true);
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {

        switch($cmd->getName()){

            case "hub";

                $LobbyTitle = new Config($this->getDataFolder() . "Title.yml", Config::YAML);
				$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

                $sender->sendMessage($this->prefix . $config->get("Hub/Lobby"));
                $sender->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                $sender->addTitle($LobbyTitle->get("LobbySendigBackTitle"));

            case "lobby";

                $LobbyTitle = new Config($this->getDataFolder() . "Title.yml", Config::YAML);
				$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

                $sender->sendMessage($this->prefix . $config->get("Hub/Lobby"));
                $sender->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
                $sender->addTitle($LobbyTitle->get("LobbySendigBackTitle"));
                return true;
        }
    }

    public function onDamage(EntityDamageEvent $ev){

        if($ev->getCause() === EntityDamageEvent::CAUSE_FALL){
            $ev->setCancelled(true);
        }

    }
	
    public function onInteract(PlayerInteractEvent $ev){

        $player = $ev->getPlayer();
        $item = $ev->getItem();
        $info = new Config($this->getDataFolder() . "info.yml", Config::YAML);
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        if($item->getCustomName() == "§aInfos"){
            $player->sendMessage($info->get("infoline1"));
            $player->sendMessage($info->get("infoline2"));
            $player->sendMessage($info->get("infoline3"));
            $player->sendMessage($info->get("infoline4"));
            $player->sendMessage($info->get("infoline5"));
            $player->sendPopup($info->get("Popup"));

        }elseif($item->getCustomName() == "§eTeleporter"){

            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(0, Item::get(138)->setCustomName("§aCityBuild"));
            $player->getInventory()->setItem(1, Item::get(2)->setCustomName("§6SkyWars"));
            $player->getInventory()->setItem(2, Item::get(35)->setCustomName("§1Wool§4Battle"));
            $player->getInventory()->setItem(6, Item::get(355, 14)->setCustomName("Bed§cWars"));
            $player->getInventory()->setItem(8, Item::get(267)->setCustomName("§4Factions"));
            $player->getInventory()->setItem(7, Item::get(399)->setCustomName("§2Lobby"));
            $player->getInventory()->setItem(4, Item::get(351, 1)->setCustomName("§cExit"));
            
            }elseif($item->getCustomName() == "§5Servers"){

            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(0, Item::get(41)->setCustomName("§6VIP Lobby"));
            $player->getInventory()->setItem(1, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(2, Item::get(42)->setCustomName("§bLobby-1"));
            $player->getInventory()->setItem(3, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(4, Item::get(42)->setCustomName("§bLobby-2"));
            $player->getInventory()->setItem(5, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(6, Item::get(42)->setCustomName("§bLobby-3"));
            $player->getInventory()->setItem(7, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));
            
            }elseif($item->getCustomName() == "§2Profile"){

            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(0, Item::get(340)->setCustomName(""));
            $player->getInventory()->setItem(1, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(2, Item::get(266)->setCustomName("§eCoins"));
            $player->getInventory()->setItem(3, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(4, Item::get(397)->setCustomName(""));
            $player->getInventory()->setItem(5, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(6, Item::get(401)->setCustomName("§dParty"));
            $player->getInventory()->setItem(7, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));

        }elseif($item->getCustomName() == "§aGadgets"){

            $player->sendPopup("§l§ePocket§aGame§8 »§b Gadgets");
			$player->getlevel()->addSound(new AnvilUseSound($player));
            $player->getInventory()->clearAll();
            if($player->hasPermission("lobby.yt")){
                $player->getInventory()->setItem(0, Item::get(377)->setCustomName("§6Effects"));
                $player->getInventory()->setItem(1, Item::get(332)->setCustomName("§dBoots"));
                $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));
                $player->getInventory()->setItem(2, Item::get(152)->setCustomName("§4Nothing"));
            }else {
                $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));
                $player->getInventory()->setItem(2, Item::get(152)->setCustomName("§4Nothing"));
                $player->getInventory()->setItem(0, Item::get(377)->setCustomName("§6Effects §7[§6Premium§7]"));
                $player->getInventory()->setItem(1, Item::get(332)->setCustomName("§dBoots §7[§6Premium§7]"));
            }

        }elseif($item->getCustomName() == "§6Effects"){

            $player->sendPopup("§6Effects");
            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(0, Item::get(263)->setCustomName("§aJumpboost"));
            $player->getInventory()->setItem(1, Item::get(266)->setCustomName("§5Speed"));
            $player->getInventory()->setItem(2, Item::get(265)->setCustomName("§3Speedboost"));
            $player->getInventory()->setItem(3, Item::get(331)->setCustomName("§cClear all effects"));
            $player->getInventory()->setItem(4, Item::get(264)->setCustomName("§fGhost"));
			$player->getInventory()->setItem(6, Item::get(32)->setCustomName("§c§lDisable all"));
            $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));

        }elseif($item->getCustomName() == "§6SkyWars"){

            $player->sendMessage("");
            $player->sendMessage($this-> prefix . Color::RED . "§6SkyWars §7teleported");
            $player->teleport(new Vector3(212, 71, 138));
            $player->getlevel()->addSound(new EndermanTeleportSound($player));
            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
                $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
                $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));

        }elseif($item->getCustomName() == "Bed§cWars"){

            $player->sendMessage("");
            $player->sendMessage($this-> prefix . Color::RED . " §7§rBed§cWars §aTeleported");
            $player->teleport(new Vector3(212, 71, 138));
            $player->getlevel()->addSound(new EndermanTeleportSound($player));
            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
                $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
                $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));

        }elseif($item->getCustomName() == "§cExit"){

            $player->getInventory()->clearAll();
			$player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
                $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
                $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));
            
            }elseif($item->getCustomName() == "§6VIP Lobby"){

            $player->sendMessage("");
            if($player->hasPermission("lobby.vip")){
            $player->sendMessage($this-> prefix . Color::RED . " §aTeleported to §6VIP Lobby");
            $player->transfer("54.37.166.24","19133");
            }else{
           $player->sendMessage($this-> prefix . Color::RED . " §7You need a rank!");
            }
           $player->sendMessage($this-> prefix . Color::RED . " §7Enter our Discord for more information!");
           
           }elseif($item->getCustomName() == "§bLobby-1"){

            $player->sendMessage("");
            $player->sendMessage($this-> prefix . Color::RED . " §7You are already §bLobby-1§7!");
            
            }elseif($item->getCustomName() == "§bLobby-2"){

            $player->sendMessage("");
            $player->transfer("54.37.166.24","19134");
            
            }elseif($item->getCustomName() == "§bLobby-3"){

            $player->sendMessage("");
            $player->transfer("54.37.166.24","19135");
            
            }elseif($item->getCustomName() == "§1language"){

            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aDeutsch"));
            $player->getInventory()->setItem(0, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(1, Item::get(339)->setCustomName("§eEnglish"));
            $player->getInventory()->setItem(2, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(3, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(5, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(6, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(8, Item::get(160)->setCustomName(""));
            $player->getInventory()->setItem(4, Item::get(351, 1)->setCustomName("§cExit"));
            
           }elseif($item->getCustomName() == "§aLangs"){

            $player->sendMessage($this-> prefix . Color::RED . " §7Your language has been set to German"); 
            
            }elseif($item->getCustomName() == "§eEnglish"){

            $player->sendMessage($this-> prefix . Color::RED . " §eSeted your lang to §aEnglish"); 
            
            }elseif($item->getCustomName() == "§eCoins"){

            $player->sendMessage($this-> prefix . Color::RED . " §eYour coins: §b1000 §eCoins");
            
            }elseif($item->getCustomName() == ""){

            $player->sendMessage($this-> prefix . Color::RED . " §7Unfortunately you have no §cfriends");            
            
            }elseif($item->getCustomName() == "§dParty"){

            $player->sendMessage($this-> prefix . Color::RED . " §cYou need §bMVP §crank!");            
            
        }elseif($item->getCustomName() == "§aJumpboost") {

            $player->removeAllEffects();
            $eff = new EffectInstance(Effect::getEffect(Effect::JUMP) , 500 * 20 , 1 , false);
            $player->addEffect($eff);
            $player->sendMessage($this->prefix . Color::WHITE . " §aEnabeld §eJumpBoost");
            $player->sendPopup("§aJumpboost§7: §eEnabled");
			$player->getInventory()->clearAll();
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));

        }elseif($item->getCustomName() == "§3Speedboost") {

            $player->removeAllEffects();
            $eff = new EffectInstance(Effect::getEffect(Effect::SPEED) , 500 * 20 , 1 , false);
            $player->addEffect($eff);
            $player->sendMessage($this->prefix . Color::WHITE . " §aEnabled §eJumpBoost");
            $player->sendPopup("§3Speedboost§7: §eEnabled");
			$player->getInventory()->clearAll();
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));

        }elseif($item->getCustomName() == "§fGhost"){

            $player->removeAllEffects();
            $eff = new EffectInstance(Effect::getEffect(Effect::INVISIBILITY) , 500 * 20 , 1 , false);
            $player->addEffect($eff);
            $player->sendMessage($this->prefix . Color::WHITE . " §aEnabled §f§lGhost§r mode");
            $player->sendPopup("§fGhost§7: §eEnabled");
			$player->getInventory()->clearAll();
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTelepprter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));
            
            }elseif($item->getCustomName() == "") {

            $player->removeAllEffects();
            $eff = new EffectInstance(Effect::getEffect(Effect::BLINDNESS) , 500 * 20 , 1 , false);
            $player->addEffect($eff);
            $player->sendMessage($this->prefix . Color::WHITE . " §7You selected the §5Blindness§r §7effect");
            $player->sendPopup("§BLINDNESS §7: §eEnabled");
			$player->getInventory()->clearAll();
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));
            
            }elseif($item->getCustomName() == "§cClear All") {

            $player->removeAllEffects();
            $eff = new EffectInstance(Effect::getEffect(Effect::NAUSEA) , 500 * 20 , 1 , false);
            $player->addEffect($eff);
            $player->sendMessage($this->prefix . Color::WHITE . " §7You have selected the §cnausea§r §7effect");
            $player->sendPopup("§cnausea §7: §cenabled");
			$player->getInventory()->clearAll();
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));

        }elseif($item->getCustomName() == "§fFly"){


            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(2, Item::get(351, 10)->setCustomName("§aEnable"));
            $player->getInventory()->setItem(6, Item::get(351, 8)->setCustomName("§4Disable"));
            $player->getInventory()->setItem(4, Item::get(351, 1)->setCustomName("§cExit"));

        }elseif($item->getCustomName() == "§aEnable"){

            $player->setAllowFlight(true);
            $player->sendMessage($this->prefix . Color::WHITE . " §r§aYou have been enabled §bFly mode");
            $player->sendPopup("§bFly mode §aEnabled");

        }elseif($item->getCustomName() == "§4Disable"){

            $player->setAllowFlight(false);
            $player->setHealth(20);
            $player->setFood(20);
            $player->sendMessage($this->prefix . Color::WHITE . " §r§cYou have disabeld §bFly mode");
            $player->sendPopup("§bFly mode §cdisabled");

        }elseif($item->getCustomName() == "§eHide Players §8[§cOff§8]"){

            $player->getInventory()->setItem(1, Item::get(280)->setCustomName("§eHide Players §8[§aOn§8]"));
            $this->hideall[] = $player;
            $player->sendMessage ($this->prefix . " §aHided Players");

        }elseif($item->getCustomName() == "§eHide Players §8[§aOn§8]"){

            unset($this->hideall[array_search($player, $this->hideall)]);
            foreach($this->getServer()->getOnlinePlayers() as $p){
                $player->showPlayer($p);
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));
            $player->sendMessage ($this->prefix . " §aHided Players");

        }elseif($item->getCustomName() == "§dBoots") {
			
            $player->getInventory()->clearAll();
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(0, Item::get(309)->setCustomName("§7iron Shoes"));
            $player->getInventory()->setItem(1, Item::get(313)->setCustomName("§1diamond shoes"));
			$player->getInventory()->setItem(6, Item::get(32)->setCustomName("§c§lTurn off"));
            $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));

        }elseif($item->getCustomName() == "§fFly §7[§6Premium§7]"){

            $player->sendMessage($this->prefix . " §7This function is allowed only §6Premium§7 Use players!");

        }elseif($item->getCustomName() == "§2Lobby"){

            $player->sendMessage($this->prefix . $config->get("Hub/Lobby"));
            $player->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
            $player->addTitle("§6Lobby", "");
            $player->getInventory()->clearAll();
            $player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
                $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
                $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));

        }elseif($item->getCustomName() == "§6Effekte §7[§6Premium§7]"){

            $player->sendMessage($this->prefix . " §7This function is allowed only §6Premium§7 Use players!");

        }elseif($item->getCustomName() == "§dBoots §7[§6Premium§7]"){

            $player->sendMessage($this->prefix . " §7This function is allowed only §6Premium§7 Use players!");
			
        }elseif($item->getCustomName() == "§7iron Shoes"){
			
			$player->getInventory()->clearAll();
			$player->getArmorInventory()->setBoots(Item::get(Item::IRON_BOOTS));
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide Players §8[§cOff§8]"));
			$player->sendMessage($this->prefix . " §7You put on the iron shoes");
			
			}elseif($item->getCustomName() == "§1diamond shoes"){
			
			$player->getInventory()->clearAll();
			$player->getArmorInventory()->setBoots(Item::get(Item::DIAMOND_BOOTS));
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(7, Item::get(339)->setCustomName("§aInfos"));
            $player->getInventory()->setItem(0, Item::get(345)->setCustomName("§eTeleporter"));
            $player->getInventory()->setItem(2, Item::get(399)->setCustomName("§5Servers"));
            $player->getInventory()->setItem(6, Item::get(388)->setCustomName("§2Profile"));
            $player->getInventory()->setItem(8, Item::get(54)->setCustomName("§aGadgets"));
            if($player->hasPermission("lobby.yt")){
            $player->getInventory()->setItem(4, Item::get(288)->setCustomName("§fFly"));
            }else{
            $player->getInventory()->setItem(4, Item::get(152)->setCustomName("§fFly §7[§6Premium§7]"));
            }
            $player->getInventory()->setItem(1, Item::get(369)->setCustomName("§eHide players §8[§cOff§8]"));
			$player->sendMessage($this->prefix . " §7You put on the diamond shoes");
			
		}elseif($item->getCustomName() == "§c§lDisable all"){
			
			$player->removeAllEffects();
			$player->sendMessage($this->prefix . " §7You have all effects and boots §cdisabled§r");
			
		}elseif($item->getCustomName() == "§c§lDisable all"){
			
			$player->getInventory()->clearAll();
			$player->sendMessage($this->prefix . " §r§cYou ahve been disabled iron Boots");
			$player->getInventory()->setSize(9);
            $player->getInventory()->setItem(0, Item::get(309)->setCustomName("§7iron Shoes"));
			$player->getInventory()->setItem(6, Item::get(32)->setCustomName("§c§lTurn off"));
            $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));
			
		}elseif($item->getCustomName() == "§cExit"){
			
			$player->getInventory()->clearAll();
			$player->getInventory()->setSize(9);
			if($player->hasPermission("lobby.yt")){
                $player->getInventory()->setItem(0, Item::get(377)->setCustomName("§6Effects"));
                $player->getInventory()->setItem(2, Item::get(38)->setCustomName("§dBoots"));
                $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));
                $player->getInventory()->setItem(1, Item::get(160)->setCustomName("§7-"));
            }else {
                $player->getInventory()->setItem(8, Item::get(351, 1)->setCustomName("§cExit"));
                $player->getInventory()->setItem(1, Item::get(160)->setCustomName("§7-"));
                $player->getInventory()->setItem(0, Item::get(377)->setCustomName("§6Effects §7[§6Premium§7]"));
                $player->getInventory()->setItem(2, Item::get(38)->setCustomName("§dBoots §7[§6Premium§7]"));
            }
			
		}

    }

}
