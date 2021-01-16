<?php
/* db connection, prepare, execute: https://phpdelusions.net/pdo/pdo_wrapper
# Table creation
	DB::query("CREATE temporary TABLE pdowrapper (id int auto_increment primary key, name varchar(255))");
# Prepared statement multiple execution
	$stmt = DB::prepare("INSERT INTO pdowrapper VALUES (NULL, ?)");
	foreach (['Sam','Bob','Joe'] as $name)	{
		$stmt->execute([$name]);
	}
# Getting rows in a loop
	$stmt = DB::run("SELECT * FROM pdowrapper");
	while ($row = $stmt->fetch(PDO::FETCH_LAZY)) {
	}
# Getting one row  
	$id  = 1;
	$row = DB::run("SELECT * FROM pdowrapper WHERE id=?", [$id])->fetch();
# Getting single field value
	$name = DB::run("SELECT name FROM pdowrapper WHERE id=?", [$id])->fetchColumn();
	var_dump($name);
# Getting array of rows
	$all = DB::run("SELECT name, id FROM pdowrapper")->fetchAll(PDO::FETCH_KEY_PAIR);
# Update
	$new = 'Sue';
	$stmt = DB::run("UPDATE pdowrapper SET name=? WHERE id=?", [$new, $id]);
**/
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
            $dsn = "sqlite:ra.sqlite3"; //.dirname(__FILE__)."/". C_DEFAULT_DB_PATH ."/".C_DEFAULT_DB_SQLITE3

            //$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            //self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
            self::$instance = new PDO($dsn,"","",$opt);
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

try { 
	//echo "sqlite:". __DIR__ ."/ra.sqlite3"; 
	//function upsert ($table,$kv,$ids)
	
	$kv = [
		 "id"       => 5
		,"breed"    => "gatto2"
		,"name"     => "spritz"
		,"age"      => time() 
		,"bboolean" => 0
		];
	$s=[]; $si=[]; $vs=[]; $w=[]; $vw=[];
	foreach($kv as $k => $v) {
		if (in_array($k,['id','user_id','pincopallo_id'])) { $w[] = sprintf("%s=?", $k); $vw[] = $v; }
		else                                               { $s[] = sprintf("%s=?", $k); $vs[] = $v; $si[]=$k; }
	}
	$stmt = DB::run("UPDATE dogs SET ". join(",",$s) ." WHERE ". join(",",$w), array_merge($vs,$vw) );
	echo "<br>rowCount=",$stmt->rowCount();
	
	
	$stmt = DB::run("INSERT OR IGNORE INTO dogs (". join(",",$si) .") values (". join(',', array_fill(0, count($si), '?')) .")", $vs );
	echo "<br>rowCount=",$stmt->rowCount();
	
	$all = DB::run("SELECT * FROM dogs")->fetchAll();
	echo "<pre>",print_r($all,true),"</pre>";  
} 
catch (PDOException $e) {print_r($e->getMessage()); }
catch (Exception $e) {print_r($e->getMessage()); }