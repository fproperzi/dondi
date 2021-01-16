<?




class DB {
    protected static $instance = null;

    protected function __construct() {}
    protected function __clone() {}

    public static function instance() {
        if (self::$instance === null) {
			$opt = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => FALSE,
            );
            $dsn = "sqlite::memory:";
            self::$instance = new PDO($dsn,NULL,NULL,$opt);
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args) {
        return call_user_func_array(array(self::instance(), $method), $args); //Call the self::instance->method($args) 
    }

    public static function run($sql, $args = []) {
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
}

class NestedSet extends  DB {
    public $table = null; 
    public $label = null;
    public $id    = null;

    
    function __construct($table,$label,$id,$pid) {
        $this->table = $table;
        $this->label = $label;
        $this->id    = $id;

    }
    protected function __clone() {}
    public function countRowsFor($w) {
        return $this->run("SELECT COUNT(1) FROM {$this->table} WHERE $w;")->fetchColumn();
    }
    public function addRoot() {

        $c = $this->countRowsFor("lvl=0");
        echo "\nc=",$c;
        if($c != '0') {
            return false;	// root exists, exit
        }
        
        $sql = "INSERT INTO {$this->table} ({$this->label}, lft, rht, lvl) VALUES(?, ?, ?, ?)";

        $r = $this->run($sql, array('root', '1', '2', '0'));
                $c = $this->countRowsFor("lvl=0");
        echo "\nc=",$c;
    }

}
try { 

    DB::run("CREATE TABLE 't_lookup' (
  'lkp_id' integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  'lkp_name' text COLLATE 'NOCASE' NOT NULL,
  'lkp_txt1' text COLLATE 'NOCASE' NULL,
  'lkp_txt2' text COLLATE 'NOCASE' NULL,
  'lkp_txt3' text COLLATE 'NOCASE' NULL,
  'lkp_int1' integer NULL,
  'lkp_int2' integer NULL,
  'lkp_int3' integer NULL,
  'pid' integer NULL,
  'lvl' integer NOT NULL,
  'lft' integer NOT NULL,
  'rht' integer NOT NULL,
  'ord' integer NULL,
  'update_by' integer NULL,
  'update_on' integer NULL
    );");
    $l = new NestedSet("t_lookup","lkp_name","lkp_id");
    $l->addRoot();

} 
catch (PDOException $e) {print_r($e->getMessage()); }
catch (Exception $e) {print_r($e->getMessage()); }