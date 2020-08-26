<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 17/01/2018
 * Time: 17:29
 */

namespace Echelon;

class Instance
{
    // Officially Supported games.
    public static $supportedGames = [
        "q3a" => "Quake 3 Arena",
        "cod" => "Call of Duty",
        "cod2" => "Call of Duty 2",
        "cod4" => "Call of Duty: Modern Warfare",
        "cod5" => "Call of Duty: World at War",
        "cod6" => "Call of Duty: Modern Warfare 2",
        "cod7" => "Call of Duty: Black Ops",
        "moh" => "Medal of Honor",
        "bfbc2" => "Battlefield: Bad Company 2",
        "iourt41" => "Urban Terror",
        "etpro" => "Enemy Territory",
        "wop" => "World of Padman",
        "smg" => "Smokin' Guns",
        "smg11" => "Smokin' Guns 1.1",
        "oa081" => "Open Arena",
        "alt" => "Altitude"
    ];

    public $config;

    public function __construct( array $config )
    {
        $this->config = $config;
        $this->gamesPath = ROOT . 'app/config/games/';
    }

    public function getActiveGames() : array  {
        return array_filter($this->getGames(), function ($game) {
           return isset($game->active) && $game->active;
        });
    }

    public function getGames() : array
    {
        $files = scandir($this->gamesPath);
        $gamesJsons = array_filter($files, function ($file) {
            return (strpos($file, '.json') !== false);
        });

        $games = [];
        foreach ($gamesJsons as $json) {
            $key = explode('.', $json)[0];
            $games[$key] = json_decode(file_get_contents($this->gamesPath . $json));
        }

        return $games;
    }

    public function updateGameServers($gameId, $remove = false) {
        $file = $this->gamesPath . $gameId . '.json';
        if (!file_exists($file)) {
            return;
        }

        $json = json_decode(file_get_contents($file));
        $json->servers = $remove ? $json->servers - 1 : $json->servers + 1;

        file_put_contents($file, json_encode($json));
    }
}