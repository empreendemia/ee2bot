<?php
class Database {

    var $db_config = 'default';
    var $db_name = '';
    var $db_host = '';
    var $db_user = '';
    var $db_pwd = '';
    var $link = null;
    
    function Database($database = 'default') {
        include(BASE.'/config/database.php');
        $db = new DatabaseConfig();

        $this->db_host = $db->{$database}['host'];
        $this->db_user = $db->{$database}['login'];
        $this->db_pwd = $db->{$database}['password'];
        $this->db_name = $db->{$database}['database'];
        $this->db_config = $database;

        $this->connect($database);
    }

    function connect() {
        $this->link = mysql_connect($this->db_host, $this->db_user, $this->db_pwd) or die(mysql_error());
        mysql_select_db($this->db_name) or die('Erro!');

        return $this->link;
    }

    function disconnect() {
        mysql_close($this->link);
    }

    function query($query) {
        $result = mysql_query($query) or die(mysql_error());

        $results = array();

        while($row = mysql_fetch_row($result)) {
            $results[] = $row;
        }

        return $results;
    }

}
?>