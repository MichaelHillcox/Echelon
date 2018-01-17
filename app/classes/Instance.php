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
    public $config;

    public function __construct( array $config )
    {
        $this->config = $config;
    }

}