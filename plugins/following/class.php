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
     * note: this is needed as a work around for the app/bootstrap.php file do not change
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

    public $tableName = "following";
    private function getTables() {
        $db = B3Database::getPointer(); // get the db pointer

        $query = "SELECT d.id,d.client_id,d.admin_id,d.time_add,d.reason,c.name,a.name as admin_name FROM ". $this->tableName ." AS d
                  INNER JOIN clients AS c ON d.client_id = c.id
                  INNER JOIN clients AS a ON d.admin_id = a.id
                  ORDER BY d.time_add DESC LIMIT 60";

        return $db->query($query); // run the query
    }

    public function returnPage() {
        $data = $this->getTables();

        $render = "<div class=\"page-header\">
            <h1>B3 Follows</h1>
            <p>Here you can see all of the players that are being followed by B3's Following plugin</p>
        </div>";

        $render .= '
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Admin</th>
                        <th>Added</th>
                        <th>Reason</th>
                    </tr>
                </thead>
                
                <tbody>';

        foreach ($data as $player):
            $player = $player[0];
            if( empty($player['id']) )
                continue;

            $time = date("mS D Y - g:ia", $player['time_add'] );

            $render .= <<<EOD
            <tr>
                <td>{$player['id']}</td>
                <td><a href="clientdetails.php?id={$player['client_id']}">{$player['name']}</a></td>
                <td><a href="clientdetails.php?id={$player['admin_id']}">{$player['admin_name']}</a></td>
                <td>{$time}</td>
                <td>{$player['reason']}</td>
            </tr>
EOD;
        endforeach;

        $render .= '
                </tbody>
            </table>';

        return $render;
    }

    public function returnCSS() {
        return "";
    }

    public function returnJS() {
        return "";
    }

}