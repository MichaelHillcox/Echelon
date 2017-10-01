<?php
/**
 * User: michael
 * Date: 28/08/2017
 * Time: 16:18
 */

if (!empty($_SERVER['SCRIPT_FILENAME']) && 'class.php' == basename($_SERVER['SCRIPT_FILENAME']))
    die ('Please do not load this page directly. Thanks!');

class following extends Plugins {

    /**
     * Gets the current instance of the class, there can only be one instance (this make the class a singleton class)
     * note: this is needed as a work around for the inc.php file do not change
     *
     * @return object $instance - the current instance of the class
     */
    public static function getInstance() {
        if (!(self::$instance instanceof self))
            self::$instance = new self();

        return self::$instance;
    }

    public function __construct() {
        parent::__construct();

        parent::setTitle('Following');
        parent::setDescription("Allows you see players that are being following by b3");
        parent::setVersion(1.0);
    }

    // Set up nav
    public function returnNav() {

        global $mem, $page; // get pointer to the members class
        if($mem->reqLevel(__CLASS__)) :
            if($page == __CLASS__)
                $data = '<li class="active">';
            else
                $data = '<li>';

            $data .= '<a href="'. PATH .'plugin.php?pl='.__CLASS__.'" title="Find players being watched">Following</a></li>';

            return $data;
        endif;

    }

    public function returnPage() {
        $render = "";

        return $render;
    }

    public function returnCSS() {
        return "";
    }

    public function returnJS() {
        return "";
    }

}