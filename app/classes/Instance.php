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
    }

}