<?php

namespace Echelon;

// TODO Expand
class Helper
{
    private $instance;

    public function __construct(Instance $instance)
    {
        // TODO, refactor this, we need DI 
        $this->instance = $instance;
    }

    /**
     * Checks if a password contains any unwanted characters
     *
     * @param string $pw - password string
     * @return bool
     */
    public static function testPW($pw) {
        // no space
        if(preg_match('# #', $pw))
            return false;

        // no dbl quote
        if(preg_match('#"#', $pw))
            return false;

        // no single quote
        if(preg_match("#'#", $pw))
            return false;

        // no equals signs
        if(preg_match("#=#", $pw))
            return false;

        return true;
    }
}
