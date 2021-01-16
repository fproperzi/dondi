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



	$kv = array(  // save info for admin
		 'user_name'   => 'admin'
		,'user_email'  => 'fproperzi@gmail.com'
		,'user_hpwd'   => '$2y$10$dLm..Mt86Uj7hioHMThInOGXmUM5R8Ssz4v78cKIPcdSx4c37c2vq'
		,'user_level'  => 8
		,'user_ctime'  => time()
		,'user_key'    => 'e34080c6-2278-4fd9-9c00-b421651ae63b'
		,'user_status' => 0
	);
	
// UPDATE t SET a = 'pdf' WHERE id = 2;
// INSERT INTO t(id, a) SELECT 2, 'pdf' WHERE changes() = 0;
/***
	function sfSQLUpsert($table,$kv,$ids) {
	
		foreach($kv as $k => $v) {
			if (in_array($k,$ids)) $w[] = sprintf("%s='%s'",$k,$v);
			else                   $s[] = sprintf("%s='%s'",$k,$v);
		}
		
		$cols = join(",",array_keys($kv));      //fields  
		$vals = join("','",array_values($kv));  // values (' $v ')
		$sset = join(",",$s);
		$wwhere = join(" AND ", $w);
			
		$q  = "";
		$q .= "UPDATE $table SET $sset WHERE $wwhere;";
		$q .= "INSERT INTO $table ($cols) SELECT '$vals' WHERE changes()=0;";
		//$q .= "SELECT * FROM $table WHERE $wwhere;";
		
		return $q;
	}  
	
	
	$sql = sfSQLUpsert("t_users",$kv,['user_name']);
	$stmt = DB::query($sql);
	
	echo "rowCount=",$stmt->rowCount();
	
	$all = DB::run("SELECT * FROM t_users")->fetchAll();
	echo "<pre>",print_r($all,true),"</pre>";  
	
	exit;

//-------------------------------------------------
	function sfSQLUpsert2($table,$kv,$ids) {
	
		foreach($kv as $k => $v) {
			$c[] = sprintf("%s" ,$k);
			$m[] = sprintf(":%s",$k);
			if (in_array($k,$ids)) { $w[] = sprintf("%s=:%s",$k,$k); $ww[] = sprintf("%s='%s'",$k,$v); }
			else                     $s[] = sprintf("%s=:%s",$k,$k);
		}
		
		$cols = join(",",$c);      
		$vals = join(",",$m);  // values (' $v ')
		$sset = join(",",$s);
		$wwhere = join(" AND ", $w);
			
		$q['update'] = "UPDATE $table SET $sset WHERE $wwhere";
		$q['insert'] = "INSERT INTO $table ($cols) VALUES ($vals)";
		$q['select'] = "SELECT * FROM $table WHERE ". join(" AND ", $ww);
		
		return $q;
	} 
	
	$q = sfSQLUpsert2("t_users",$kv,['user_name']);
	echo "<pre>queries:",print_r($q,true),"</pre>";   
	
	echo "<h3>update</h3>";
	$stmt = DB::run($q['update'] ,$kv);
	if($stmt->rowCount()==0) {
		echo "<h3>insert</h3>";
		$stmt = DB::run($q['insert'] ,$kv);
	}
	echo "<h3>select</h3>"; 
	$row = DB::run($q['select'])->fetch();
	
	echo "<pre>row:",print_r($row,true),"</pre>";   
	
		$all = DB::run("SELECT * FROM t_users")->fetchAll();
	echo "<pre>",print_r($all,true),"</pre>";  
	
***/	
	
	function afSQLiteUpsert($table,$kv,$ids) { 
	
		foreach($kv as $k => $v) {
			$c[] = sprintf("%s" ,$k);
			$m[] = sprintf(":%s",$k);
			if (in_array($k,$ids)) { $w[] = sprintf("%s=:%s",$k,$k); $ww[$k] = $v; }
			else                     $s[] = sprintf("%s=:%s",$k,$k);
		}
		
		$cols = join(",",$c);      
		$vals = join(",",$m);  
		$sset = join(",",$s);
		$wwhere = join(" AND ", $w);
			
		$stmt = DB::run("UPDATE $table SET $sset WHERE $wwhere" ,$kv);	
		if($stmt->rowCount()==0) 
			$stmt = DB::run("INSERT INTO $table ($cols) VALUES ($vals)" ,$kv);
		
		$r = DB::run("SELECT * FROM $table WHERE $wwhere",$ww)->fetch();

		
		return $r;
	} 
	
	$row = afSQLiteUpsert("t_users",$kv,['user_name']);
	echo "<pre>",print_r($row,true),"</pre>";  
} 
catch (PDOException $e) {print_r($e->getMessage()); }
catch (Exception $e) {print_r($e->getMessage()); }