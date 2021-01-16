<?php

error_reporting(E_ALL); 
ini_set('display_errors', 1); 
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
	protected $trans = false;

	protected function __construct() {}
	protected function __clone() {}

	public static function instance() { 
		if (self::$instance === null) {
			$opt = array(
				PDO::ATTR_ERRMODE			=> PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES   => FALSE,
			);
			$dsn = "sqlite:ra.sqlite3"; //.dirname(__FILE__)."/". C_DEFAULT_DB_PATH ."/".C_DEFAULT_DB_SQLITE3
			//$dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
			
			self::$instance = new PDO($dsn,null,null,$opt); //self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
		}
		return self::$instance;
	}

	public static function __callStatic($method, $args) {
		return call_user_func_array(array(self::instance(), $method), $args); //Call the self::instance->method($args) 
	}

	public static function run($sql, $args = []) {
		//echo "<pre>",$sql,print_r($args,true),"</pre>";
		$stmt = self::instance()->prepare($sql);
		$stmt->execute($args);
		return $stmt;
	}
    //no static used in class
	public function transStart() {
		if($this->trans) return;
		$this->trans = true;
		$this->instance()->beginTransaction();
	}
	public function transEnd() {
		$this->instance()->commit();
        $this->trans = false;
	}
    public function transBack() {
        if($this->trans)
            $this->instance()->rollBack();
    }
}
//https://eval.in/779206
//https://eval.in/779208
//based on: https://github.com/ben-nsng/nestedset-php
//ideas from: http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
class NestedSet extends DB {
	
	const C_ROOT_LABEL = "root";
	
	public $table = null; 
	public $label = null;
	public $id	  = null; 
	
	// pid,lft,rht,lvl   must!
	  
	function __construct($table,$label,$id) {
		$this->table = $table;
		$this->label = $label;
		$this->id	 = $id;
	}
	protected function __clone() {}
	
	public function countRowsFor($w,$a=[]) {
		return $this->run("SELECT COUNT(1) FROM {$this->table} WHERE $w;",$a)->fetchColumn();
	}
	public function getRow($w,$a=[]) { 
		return $this->run("SELECT * FROM {$this->table} WHERE $w;",$a)->fetch();
	}
	public function getRows($w,$a=[]) { 
		return $this->run("SELECT * FROM {$this->table} WHERE $w;",$a)->fetchAll();
	}
	public function addRow($a=[]) {
		$this->run("INSERT INTO {$this->table} ({$this->label}, lft, rht, lvl, pid) VALUES(:label,:lft,:rht,:lvl,:pid);",$a);
		return $this->instance()->lastInsertId();
	}
	// Select all nodes from the table
	public function selectAll() {
		return $this->run("SELECT {$this->id} as id, {$this->label} as label, lvl, pid,
		cast((((rht - lft) -1) / 2) as integer) AS cnt_children, 
		CASE WHEN rht - lft > 1 THEN 1 ELSE 0 END AS is_branch
		FROM {$this->table} ORDER BY lft")->fetchAll(); 
	}
	// add root node, if exist return id
	public function addRoot() {
		$r = $this->getRow("lvl=0");
		if($r === false)
			return $this->addRow(['label'=>self::C_ROOT_LABEL, 'lft'=>1, 'rht'=>2, 'lvl'=>0, 'pid'=>0]); // addRow return id by default
		else
			return $r[$this->id];
	}
	
	// add node
	public function addNode($label = "", $pid = null) {

		if(! $pid) {			         // if no parent define, add to root node
			$pid = $this->addRoot();     // root add or select if exist 
			if($label === self::C_ROOT_LABEL)	 // if root you want, here it is
				return $pid;
		}	
		
		$r = $this->getRow("{$this->id}=?",[$pid]); 
		if($r === false)
			return false;  //no parent?
		
		$plft = $r["lft"];
		$prht = $r["rht"];
		$plvl = $r["lvl"];

		$this->transStart();
		
		//shift the node to give some room for new node
		$this->run("UPDATE {$this->table} SET lft = CASE WHEN lft >  ? THEN lft + 2 ELSE lft END ,rht = CASE WHEN rht >= ? THEN rht + 2 ELSE rht END WHERE rht >= ?"
			,array($prht, $prht, $prht));
		
		$id = $this->addRow(['label'=>$label, 'lft'=>$prht, 'rht'=>($prht+1), 'lvl'=>($plvl+1), 'pid'=>$pid]); 
	
		$this->transEnd();

		return $id; 
	}
	
	// Delete existing node
	public function deleteNode($node_id) {
	   
		$r = $this->getRow("{$this->id}=?",[$node_id]);
		if($r === false) 
			return false;	//no node?

		$lft = $r["lft"];
		$rht = $r["rht"];
		$lvl = $r["lvl"];
		
		$this->transStart();

		// remove parent first
		$this->run("UPDATE {$this->table} SET pid = NULL WHERE lft >= ? AND rht <= ?", array($lft, $rht));

		// delete nodes
		//$this->run("DELETE FROM {$this->table} WHERE lft >= ? AND rht <= ?", array($lft, $rht));
		$this->run("DELETE FROM {$this->table} WHERE pid IS NULL AND lvl <> 0"); // no the root please!

		$node_tmp = $rht - $lft + 1;

		// shift other node to correct position
		$this->run("UPDATE {$this->table} SET lft = CASE WHEN lft > ? THEN lft - ? ELSE lft END, rht = CASE WHEN rht >= ? THEN rht - ? ELSE rht END WHERE rht >= ?"
			,array($lft, $node_tmp, $rht, $node_tmp, $rht));

		$this->transEnd();
	}

	//--- moving parts ---
	//check about node-from node-to
	private function checkNodes($node_id_1, $node_id_2) {
		if($node_id_1 == $node_id_2)  
			return false;	//same node
		
		$r = $this->getRows("{$this->id} in (?,?)", array($node_id_1, $node_id_2));

		if(count($r) != 2) 
			return false;	//no node	   

		if($r[0][$this->id] == $node_id_1) list($node1,$node2) = $r;
		else							   list($node2,$node1) = $r;
		
		$node1_size = $node1['rht'] - $node1['lft'] + 1;
		$node2_size = $node2['rht'] - $node1['lft'] + 1;
		
		return compact("node1","node2","node1_size","node2_size"); 
	}
	// Move existing node1 into node 2
	public function addChild($node_id_1, $node_id_2) {
		
		$a = $this->checkNodes($node_id_1, $node_id_2);
		if(!$a) return false;
		else    expand($a);

		$this->transStart();

		// temporary "remove" moving node
		$this->run("UPDATE {$this->table} SET lft = 0 - lft ,rht = 0 - rht ,lvl = lvl + (?) WHERE lft >= ? AND rht <= ?", array(
			$node2['lvl'] - $node1['lvl'] + 1, $node1['lft'], $node1['rht']));

		// decrease left / right position for current node
		$this->run("UPDATE {$this->table} SET lft = lft - (?) WHERE lft >= ?", array($node1_size, $node1['lft']));
		$this->run("UPDATE {$this->table} SET rht = rht - (?) WHERE rht >= ?", array($node1_size, $node1['rht']));

		// increase left / right position for future node
		$this->run("UPDATE {$this->table} SET lft = lft + (?) WHERE lft >= ?", array($node1_size, $node2['rht'] > $node1['rht'] ? $node2['rht'] - $node1_size : $node2['rht']));
		$this->run("UPDATE {$this->table} SET rht = rht + (?) WHERE rht >= ?", array($node1_size, $node2['rht'] > $node1['rht'] ? $node2['rht'] - $node1_size : $node2['rht']));

		// move the node to new position
		$this->run("UPDATE {$this->table} SET lft = 0 - lft + (?),rht = 0 - rht + (?) WHERE lft <= ? AND rht >= ?", array(
			$node2['rht'] > $node1['rht'] ? $node2['rht'] - $node1['rht'] - 1 : $node2['rht'] - $node1['rht'] - 1 + $node1_size,
			$node2['rht'] > $node1['rht'] ? $node2['rht'] - $node1['rht'] - 1 : $node2['rht'] - $node1['rht'] - 1 + $node1_size,
			0 - $node1['lft'], 0 - $node1['rht']));

		// update parent
		$this->run("UPDATE {$this->table} SET pid = ? WHERE	{$this->id} = ?", array($node2[$this->id], $node1[$this->id]));

		$this->transEnd();
	}
	// Move existing node1 before node 2
	public function addBefore($node_id_1, $node_id_2) {
		
		$a = $this->checkNodes($node_id_1, $node_id_2);
		if(!$a) return false;
		else    expand($a);

		$this->transStart();

		// if not in same level, put it in same level
		if($node1['lvl'] != $node2['lvl'] || $node1['pid'] != $node2['pid']) {
			$this->addChild($node_id_1, $node2['pid']);
			return $this->addBefore($node_id_1, $node_id_2);
		}

		// temporary "remove" moving node
		$this->run("UPDATE {$this->table} SET lft = 0 - lft,rht = 0 - rht WHERE lft >= ? AND rht <= ?", array($node1['lft'], $node1['rht']));
		
		if($node1['lft'] > $node2['lft']) {	//move left
			//shift the node to right to give some room
			$this->run("UPDATE {$this->table} SET lft = lft + ?,rht = rht + ? WHERE lft >= ? AND rht <= ?", array($node1_size, $node1_size, $node2['lft'], $node1['lft']));
			
            //move back the node1
			$this->run("UPDATE {$this->table}  SET lft = 0 - lft - ? ,rht = 0 - rht - ? WHERE lft <= ? AND rht >= ?"
				,array($node1['lft'] - $node2['lft'], $node1['lft'] - $node2['lft'], 0 - $node1['lft'], 0 - $node1['rht']));
		}
		else {
			//shift the node to left to give some room
			$this->run("UPDATE {$this->table} SET lft = lft - ?,rht = rht - ? WHERE lft >= ? AND rht < ?"
				,array($node1_size, $node1_size, $node1['rht'], $node2['lft']));
            
			//move back the node1
			$this->run("UPDATE {$this->table} SET lft = 0 - lft + ? ,rht = 0 - rht + ? WHERE lft <= ? AND rht >= ?"
				,array($node2['lft'] - $node1['rht'] - 1, $node2['lft'] - $node1['rht'] - 1, 0 - $node1['lft'], 0 - $node1['rht']));
            
		}

		$this->transEnd();
	}

	// Move existing node after node 2
	public function addAfter($node_id_1, $node_id_2) {

		$a = $this->checkNodes($node_id_1, $node_id_2);
		if(!$a) return false;
		else    expand($a);

		$this->transStart();

		// if not in same level, put it in same level
		if($node1['lvl'] != $node2['lvl'] || $node1['pid'] != $node2['pid']) {
			$this->addChild($node_id_1, $node2['pid']);
			return $this->addAfter($node_id_1, $node_id_2);
		}

		// temporary "remove" moving node
		$this->run("UPDATE {$this->table} SET lft = 0 - lft ,rht = 0 - rht WHERE lft >= ? AND rht <= ?", array($node1['lft'], $node1['rht']));
		
		if($node1['lft'] > $node2['lft']) {	//move left
			//shift the node to right to give some room
			 $this->run("UPDATE {$this->table} SET lft = lft + ? ,rht = rht + ? WHERE lft > ? AND rht <= ?", array($node1_size, $node1_size, $node2['rht'], $node1['lft']));

			//move back the node1
			 $this->run("UPDATE {$this->table} SET lft = 0 - lft - ? ,rht = 0 - rht - ? WHERE lft <= ? AND rht >= ?"
				,array($node1['lft'] - $node2['rht'] - 1, $node1['lft'] - $node2['rht'] - 1, 0 - $node1['lft'], 0 - $node1['rht']));
		}
		else {
			//shift the node to left to give some room
			$this->run("UPDATE {$this->table} SET lft = lft - ? ,rht = rht - ? WHERE lft >= ? AND rht <= ?", array($node1_size, $node1_size, $node1['rht'], $node2['rht']));

			//move back the node1
			$this->run("UPDATE {$this->table} SET lft = 0 - lft + ? ,rht = 0 - rht + ? WHERE lft <= ? AND rht >= ?"
				,array($node2['rht'] - $node1['rht'], $node2['rht'] - $node1['rht'], 0 - $node1['lft'], 0 - $node1['rht']));

		}

		$this->transEnd();
	}
	// from "red/white/green/"  to "root/red/white/green" 
	public function fixPath($path, $suffix = self::C_ROOT_LABEL) { 
		if ($path[0] === "/")                   $path = substr ( $path, 1);  						
		if ($path [strlen( $path ) - 1] == "/")	$path = substr ( $path, 0, strlen ( $path ) - 1 );	
		if (substr ( $path, 0, strlen($suffix) ) !== $suffix) 
			                                    $path = $suffix ."/". $path;				
		return $path;	
	}
    // from path to id: from "/role/editor/editor_clip" to #48
    public function path2Id($path) {
 
		$path = $this->fixPath($path);  
		$a = explode ( "/", $path );

        // mysql: GROUP_CONCAT(parent.Title ORDER BY parent.Lft SEPARATOR '/')
		$r = $this->run ( "SELECT n.{$this->id},GROUP_CONCAT(p.{$this->label},'/') AS path FROM {$this->table} AS n,{$this->table} AS p
                WHERE n.lft BETWEEN p.lft AND p.rht	AND  n.{$this->label}=?	GROUP BY n.{$this->id} HAVING path = ?
                ", [$a [count ( $a ) - 1], $path] )->fetchAll();
                
        if($r) return $r[0][$this->id];
		else   return null;
    }
    // from id to path: from #48 to "root/role/editor/editor_clip"
    public function id2Path($id) {
        $r = $this->run( "SELECT GROUP_CONCAT(p.{$this->label},'/') AS path FROM {$this->table} AS n,{$this->table} AS p	
                WHERE n.lft BETWEEN p.lft AND p.rht	AND n.{$this->id} = ? GROUP BY n.{$this->id}  
                ",[$id])->fetchColumn();
  
        if($r) return $r;
        else   return null;
    }
    // add all nodes in path if not exists, return leaf id: root/roles/staff/tecnical/coach -> return coach role_id 
	public function addPath($path) {

		$path = $this->fixPath($path); 
		$a = explode ( "/", $path );

		$pid = null;

	    for($i=1; $i<=count($a); $i++) {		

	        $sCurrentPath = join("/", array_slice($a,0,$i) ); 
	        $id = $this->path2Id($sCurrentPath);	//exist?

			//echo sprintf("\n%2d -> %s",$id,$sCurrentPath );

	        if (! $id) {
	           $id = $this->addNode($a[$i-1],$pid); // label,pid  
			   // echo "\naddNode=",$a[$i-1],"=",$pid;
	        }
	        $pid = $id;
	    }
	    return $id; 
	}

} 
/**
	RBAC nested set, "one table fit all":
	id 			INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	label		text NOT NULL,
	note		text NULL,
	user_id		INTEGER NULL,
	role_id		INTEGER NULL,
	perm_id		INTEGER NULL,
	pid			INTEGER NOT NULL
	lvl			INTEGER NOT NULL
	lft			INTEGER NOT NULL
	rht			INTEGER NOT NULL
	
utils: 
	
	$r = DB::run("
	-- user permission path form --------------
	SELECT n.id,GROUP_CONCAT(p.label,'/') AS path
	FROM t_rbac AS n,t_rbac AS p 
	WHERE n.lft BETWEEN p.lft AND p.rht AND n.id in (
	   
	    -- user permmissions id form --------------
		select n.id
		FROM t_rbac AS n,t_rbac AS p 
		WHERE n.lft BETWEEN p.lft AND p.rht
		AND p.id in (select perm_id from t_rbac where role_id in 
			(select n.id
			FROM t_rbac AS n,t_rbac AS p 
			WHERE n.lft BETWEEN p.lft AND p.rht
			AND p.id in (select role_id from t_rbac where user_id=?)
			)
		)
		
	)
	GROUP BY n.id ORDER BY path
	",[$user_id])->fetchAll();	
	
 */
//idea from: https://github.com/OWASP/rbac
class Rbac extends NestedSet { 
	const C_ROLES       = "roles"; 
	const C_PERMS       = "permissions";
	const C_ROLES_PERMS = "roles_perms";
	const C_USERS_ROLES = "users_roles";
	
	function __construct($table="t_rbac",$label="label",$id="id") {
		$this->table = $table;
		$this->label = $label; 
		$this->id	 = $id;
	}
	// create node from ancestor and path: C_ROLES, staff/tecnical/coach => root/roles/staff/tecnical/coach
	private function getNode(&$s, $suffix) { //any &$s change is not lost
		if (is_numeric($s)) 
			return $s;
		$s = $this->fixPath($s, $suffix);   //$suffix . ($s[0] !== "/" ? "/$s" : $s);
		return  $this->path2Id( $s );
	}
	private function setNode($s, $suffix) {	
		$id = $this->getNode($s, $suffix);
		if($id) return $id; 
		else    return $this->addPath( $s ); 
	}
	
	public function setRoleId($role) { return $this->setNode($role, self::C_ROLES); }
	public function getRoleId($role) { return $this->getNode($role, self::C_ROLES); }	
	public function setPermId($perm) { return $this->setNode($perm, self::C_PERMS); }
	public function getPermId($perm) { return $this->getNode($perm, self::C_PERMS); }

	public function setRole2Perm($role, $permission) {
		
		$role_id = $this->setRoleId($role);
		$perm_id = $this->setPermId($permission);
		
		if(!$role_id  || !$perm_id)
			return false;

		$id = $this->addPath( sprintf("%s/R%'_9d P%'_9d",self::C_ROLES_PERMS, $role_id, $perm_id) );
		return $this->run("UPDATE {$this->table} SET role_id=$role_id,perm_id=$perm_id WHERE id=$id");
    }
	public function setUser2Role($user_id,$role) {
		
		$role_id = $this->setRoleId($role);
		
		if(!$role_id  || !$user_id)
			return false;

		$id = $this->addPath( sprintf("%s/U%'_9d R%'_9d",self::C_USERS_ROLES, $user_id, $role_id) );
		return $this->run("UPDATE {$this->table} SET role_id=$role_id,user_id=$user_id WHERE id=$id");
    }

	public function unsetRole2Perm($role, $permission) {
		
		$role_id = $this->getRoleId($role);
		$perm_id = $this->getPermId($permission);
		
		if(!$role_id  || !$perm_id)
			return false;

		$id = $this->run("SELECT id FROM {$this->table} WHERE role_id=$role_id AND perm_id=$perm_id")->fetchColumn();
		if($id) return $this->deleteNode($id);
		else    return false;
    }
	public function unsetUser2Role($user_id,$role) {
		
		$role_id = $this->setRoleId($role);
		
		$id = $this->run("SELECT id FROM {$this->table} WHERE role_id=$role_id AND user_id=$user_id")->fetchColumn();
		if($id) return $this->deleteNode($id);
		else    return false;
	}
	function userPerm($user_id, $permission) { return $this->userHasPermission($user_id, $permission); } 
	function userHasPermission($user_id, $permission) {
		$perm_id = $this->getPermId($permission);
		//echo sprintf ("\n%d,%d",$user_id,$perm_id);
		if(!$user_id  || !$perm_id)
			return false;
		
		return $this->run("select count(*) FROM t_rbac AS n,t_rbac AS p 
			WHERE n.lft BETWEEN p.lft AND p.rht
			AND p.id in (select perm_id from t_rbac where role_id in 
				(select n.id
				FROM t_rbac AS n,t_rbac AS p 
				WHERE n.lft BETWEEN p.lft AND p.rht
				AND p.id in (select role_id from t_rbac where user_id=?)
				) AND n.id=?
			)", [$user_id,$perm_id])->fetchColumn();
	}
	//usef in test
    public function reset() {
		$this->run("delete from {$this->table}");
		$this->run("delete from sqlite_sequence where name='{$this->table}'");
    }

}

try {
	echo "<pre>";
    //$l = new NestedSet("t_lookup","lkp_name","lkp_id");

	//$l->deleteNode(96); 
	//$l->addNode("lola_".time(),43);
/*
	$l->addPath("bianco/blu/verde/v1.1");
	$l->addPath("/bianco/blu/verde/v1.2");
	$l->addPath("/bianco/blu/verde/v1.3"); 
	$l->addPath("/bianco/blu/verde/v1.3/v1.3.1");
	$l->addPath("/bianco/rosa/mio/non");
	
	
	
	echo "\n\n\n------";
	$r = DB::run("SELECT n.lkp_id as id,GROUP_CONCAT(p.lkp_name,'/') AS path
    FROM t_lookup AS n,t_lookup AS p WHERE n.lft BETWEEN p.lft AND p.rht	
    GROUP BY n.lkp_id ORDER BY path")->fetchAll();
	foreach($r as $rr) echo sprintf("\n%2d -> %s",$rr['id'],$rr['path'] );
*/
	

	
	$b = new Rbac();
	$b->reset();
	
	//$b->deleteNode(1);
	/*
	echo "\n", $b->setRoleId("guest");
	echo "\n", $b->setRoleId("editor");
	echo "\n", $b->setRoleId("editor/clips");
	echo "\n", $b->setRoleId("editor/survey");
	echo "\n", $b->setRoleId("editor/stats");
	echo "\n", $b->setRoleId("secretary");
	echo "\n", $b->setRoleId("secretary/liaison-officer");
	echo "\n", $b->setRoleId("staff");
	echo "\n", $b->setRoleId("staff/tecnical");
	echo "\n", $b->setRoleId("staff/tecnical/coach");
	echo "\n", $b->setRoleId("staff/tecnical/manager");
	echo "\n", $b->setRoleId("staff/tecnical/trainer");
	echo "\n", $b->setRoleId("staff/tecnical/referee");
	echo "\n", $b->setRoleId("staff/medical");
	echo "\n", $b->setRoleId("staff/medical/doctor");
	echo "\n", $b->setRoleId("staff/medical/physio");
	echo "\n", $b->setRoleId("staff/medical/terapist");
	echo "\n", $b->setRoleId("staff/logistic");
	echo "\n", $b->setRoleId("staff/marketing");
	echo "\n", $b->setRoleId("player/forward");
	echo "\n", $b->setRoleId("player/back");
	echo "\n", $b->setRoleId("player/leader");
	
	*/
	
	
	$b->setRole2Perm("guest"				,"page/clip/read");
	$b->setRole2Perm("player"				,"page/clip/read");
	$b->setRole2Perm("staff"				,"page/clip"); 
	$b->setRole2Perm("editor/comments"				,"page/clip/comments/read");
	$b->setRole2Perm("editor"				,"page/clip");
	$b->setRole2Perm("editor/clip"			,"page/clip/edit");

	$b->setRole2Perm("player"		        ,"page/clip/comments/write"); 
	$b->setRole2Perm("staff"			    ,"page/clip/comments/read"); 
	
	
	$b->setRole2Perm("staff/tecnical/coach"		,"page/clip");  
	$b->setRole2Perm("staff/tecnical/referee"	,"page/medical/read");
	$b->setRole2Perm("staff/medical/doctor"		,"page/medical/write");
	
	
	$b->setUser2Role(66				        ,"staff/tecnical/coach");
	//$b->setUser2Role(65				    ,"editor"); 
	$b->setUser2Role(66				        ,"staff/medical");
	
	$user_id = 66;
	$perm = "page/clip/comments"; 
	echo "\nUser $user_id, Perm $perm = ",$b->userPerm($user_id,$perm) ? "yes":"no";
	
	
	//for($i=45;$i<90;$i++) $l->deleteNode($i); 
	
	$user_id=66;
	echo "\n\n permessi per l'utente $user_id\n";  
	
	$r = DB::run("select distinct n.*
		FROM t_rbac AS n,t_rbac AS p 
		WHERE n.lft BETWEEN p.lft AND p.rht
		AND p.id in (select perm_id from t_rbac where role_id in 
			(select n.id
			FROM t_rbac AS n,t_rbac AS p 
			WHERE n.lft BETWEEN p.lft AND p.rht
			AND p.id in (select role_id from t_rbac where user_id=?)
			)
		)",[$user_id])->fetchAll();		
	foreach($r as $rr) echo sprintf("\n%2d => %s",$rr['id'],$rr['label'] );
	
	echo "\n\n in forma estesa:\n";
	
	$r = DB::run("SELECT n.id,GROUP_CONCAT(p.label,'/') AS path
	FROM t_rbac AS n,t_rbac AS p 
	WHERE n.lft BETWEEN p.lft AND p.rht AND n.id in (
	
		select n.id
		FROM t_rbac AS n,t_rbac AS p 
		WHERE n.lft BETWEEN p.lft AND p.rht
		AND p.id in (select perm_id from t_rbac where role_id in 
			(select n.id
			FROM t_rbac AS n,t_rbac AS p 
			WHERE n.lft BETWEEN p.lft AND p.rht
			AND p.id in (select role_id from t_rbac where user_id=?)
			)
		)
		
	)
	GROUP BY n.id ORDER BY path",[$user_id])->fetchAll();
	foreach($r as $rr) echo sprintf("\n%2d => %s",$rr['id'],$rr['path'] );

	echo "\n\n leafs:\n";
	
	$r = DB::run("SELECT n.id,GROUP_CONCAT(p.label,'/') AS path, (n.rht-n.lft) as size
    FROM t_rbac AS n,t_rbac AS p WHERE n.lft BETWEEN p.lft AND p.rht	
	-- AND n.rht=n.lft+1  
    GROUP BY n.id HAVING size=1 ORDER BY path ")->fetchAll();
	foreach($r as $rr) echo sprintf("\n%2d (%2d) -> %s",$rr['id'], $rr['size'], $rr['path'] );
	
	
	echo "</pre>";
} 
catch (PDOException $e) {echo ($e->getMessage()); }
catch (Exception $e) {echo ($e->getMessage()); }
    

?>
<pre>


-- tutti i ruoli	
select n.*
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht
AND p.pid=1 and p.label='roles'

--tutti i ruoli in formato esteso solo leafs 
SELECT n.id,GROUP_CONCAT(p.label,'/') AS path
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht AND n.id in (
	select n.id
	FROM t_rbac AS n,t_rbac AS p 
	WHERE n.lft BETWEEN p.lft AND p.rht
	AND p.pid=1 and p.label='roles'
) AND n.rht=n.lft+1 
GROUP BY n.id ORDER BY path

--tutti i permessi
select n.*
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht
AND p.pid=1 and p.label='permissions'	

--tutti i permessi in formato esteso
SELECT n.id,GROUP_CONCAT(p.label,'/') AS path
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht AND n.id in (
	select n.id
	FROM t_rbac AS n,t_rbac AS p 
	WHERE n.lft BETWEEN p.lft AND p.rht
	AND p.pid=1 and p.label='permissions'
)
GROUP BY n.id ORDER BY path

--tutti i permessi in formato esteso senza la prima riga: root/permissions
SELECT n.id,GROUP_CONCAT(p.label,'/') AS path
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht AND n.id in (
	select n.id
	FROM t_rbac AS n,t_rbac AS p 
	WHERE n.lft BETWEEN p.lft+1 AND p.rht
	AND p.pid=1 and p.label='permissions'
)
GROUP BY n.id ORDER BY path	

-- tutti i permessi del ruolo 10
select n.*
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht
AND p.id in (select perm_id from t_rbac where role_id=10)
	
-- tutti i ruoli dell'utente 65
select n.*
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht
AND p.id in (select role_id from t_rbac where user_id=65)

-- tutti i permessi dell'utente 65
select n.*
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht
AND p.id in (select perm_id from t_rbac where role_id in 
	(select n.id
	FROM t_rbac AS n,t_rbac AS p 
	WHERE n.lft BETWEEN p.lft AND p.rht
	AND p.id in (select role_id from t_rbac where user_id=65)
	)
)
	
-- tutti i permessi dell'utente 65 in forma estesa
SELECT n.id,GROUP_CONCAT(p.label,'/') AS path
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht AND n.id in (
	select n.id
	FROM t_rbac AS n,t_rbac AS p 
	WHERE n.lft BETWEEN p.lft AND p.rht
	AND p.id in (select perm_id from t_rbac where role_id in 
		(select n.id
		FROM t_rbac AS n,t_rbac AS p 
		WHERE n.lft BETWEEN p.lft AND p.rht
		AND p.id in (select role_id from t_rbac where user_id=65)
		)
	)
)
GROUP BY n.id ORDER BY path

</pre>
