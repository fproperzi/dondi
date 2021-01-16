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
/*
	insert or ignore into <table>(<primaryKey>, <column1>, <column2>, ...) values(<primaryKeyValue>, <value1>, <value2>, ...); 
update <table> set <column1>=<value1>, <column2>=<value2>, ... where changes()=0 and <primaryKey>=<primaryKeyValue>;
select case changes() WHEN 0 THEN last_insert_rowid() else <primaryKeyValue> end;
*/	

		
	//INSERT INTO t_users_access (user_id,dd,cnt) values ($user_id,now(),1) ON DUPLICATE KEY UPDATE cnt=cnt+1	


	
	$a = ['user_id'=>69,'dd'=>date('Ymd')
	
		]; 
	
	$sSql  = "";
	//$sSql .= "INSERT OR IGNORE INTO t_users_access (user_id,dd,cnt) values (:user_id,:dd,1);";
	//$sSql .= "UPDATE t_users_access SET cnt=cnt+1 WHERE changes()=0 AND user_id=:user_id AND dd=:dd;";
	
	$sSql .= "UPDATE t_users_access SET cnt=cnt+1 WHERE user_id=:user_id AND dd=:dd;";
	$sSql .= "INSERT INTO t_users_access (user_id,dd,cnt) SELECT :user_id1,:dd1,1 WHERE (Select changes() = 0);";
	
	echo $sSql," a=<pre>",print_r($a,true),"</pre>";
	$stmt = DB::run($sSql, $a);
	echo "rowCount=",$stmt->rowCount(); 
	
	$all = DB::run("SELECT * FROM t_users_access")->fetchAll();
	echo "<pre>",print_r($all,true),"</pre>";  
	

} 
catch (PDOException $e) {print_r($e->getMessage()); }
catch (Exception $e) {print_r($e->getMessage()); }