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
			//self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
			self::$instance = new PDO($dsn,null,null,$opt);
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
// based on https://github.com/ben-nsng/nestedset-php
//http://mikehillyer.com/articles/managing-hierarchical-data-in-mysql/
class NestedSet extends DB {
	public $table = null; 
	public $label = null;
	public $id	= null; 
	
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
			return $this->addRow(['label'=>'root', 'lft'=>1, 'rht'=>2, 'lvl'=>0, 'pid'=>0]); // addRow return id by default
		else
			return $r[$this->id];
	}
	
	// add node
	public function addNode($label = "", $pid = null) {

		if(! $pid) {			       // if no parent define, add to root node
			$pid = $this->addRoot();   // root add or select if exist 
			if($label==="root")		   // to not have 2 root
				return $pid;
		}	
		
		$r = $this->getRow("{$this->id}=?",[$pid]); 
		
		if($r === false)
			return false;
		
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
			return false;	//no node

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
		else expand($a);

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
		else expand($a);

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
		else expand($a);

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
	public function checkPath($path) {
		if (substr ( $path, 0,4 ) !== "root") 
			$path = "root". ($path[0] !== "/" ? "/" : "") . $path; 

		if ($path [strlen ( $path ) - 1] == "/")
			$path = substr ( $path, 0, strlen ( $path ) - 1 );
		
		return $path;	
	}
    // from path to id: from "/role/editor/editor_clip" to #48
    public function path2Id($path) {
 
		$path = $this->checkPath($path);
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

	public function addPath($path,array $asNote = null) {

		$path = $this->checkPath($path); //echo "\naddPath=",$path;
		$a = explode ( "/", $path );
		
		$nNodesCreated = 0;
		$pid = null;

	    for($i=1; $i<=count($a); $i++) {		
			
	        if (isset ($asNote[$i]))
	            $sNote = $asNote[$i];
	        else
	            $sNote = "";

	        $sCurrentPath = join("/", array_slice($a,0,$i) ); 
	        $id = $this->path2Id($sCurrentPath);

			//echo sprintf("\n%2d -> %s",$id,$sCurrentPath );

	        if (! $id) {
	           $id = $this->addNode($a[$i-1],$pid); // label,pid  
			   // echo "\naddNode=",$a[$i-1],"=",$pid;
	            $nNodesCreated++;
	        }
	        
	            $pid = $id;

	    }
	    return $id; //(int)$nNodesCreated;
	}

}
/**
	RBAC nested set:
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
	
 */
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

	private function getNode($ppath, $s) {
		if (is_numeric($s)) 
			return $s;
		$s = $ppath . ($s[0] !== "/" ? "/$s" : $s);
		$id = $this->path2Id( $s );
		
		if($id) return $id;
		else    return $this->addPath( $s ); 
	}
	public function getRoleId($role) { return $this->getNode(self::C_ROLES,$role); }
	public function getPermId($perm) { return $this->getNode(self::C_PERMS,$perm); }


	public function setRole2Perm($role, $perm) {
		
		$role_id = $this->getRoleId($role);
		$perm_id = $this->getPermId($perm);

		$id = $this->addPath( sprintf("%s/R%'_9d P%'_9d",self::C_ROLES_PERMS, $role_id, $perm_id) );
		return $this->run("UPDATE {$this->table} SET role_id=$role_id,perm_id=$perm_id WHERE id=$id");
    }
	public function setUser2Role($user_id,$role) {
		
		$role_id = $this->getRoleId($role);

		$id = $this->addPath( sprintf("%s/U%'_9d R%'_9d",self::C_USERS_ROLES, $user_id, $role_id) );
		return $this->run("UPDATE {$this->table} SET role_id=$role_id,user_id=$user_id WHERE id=$id");
    }
	public function unassign($role, $permission) {
		
		$role_id = $this->getRoleId($role);
		$perm_id = $this->getPermId($perm);

		$id = $this->run("SELECT id FROM {$this->table} WHERE role_id=$role_id AND perm_id=$perm_id")->fetchColumn();
		if($id) return $this->deleteNode($id);
		else    return false;
    }
	function hasPermission($role, $permission) {
		
	}
    public function check($permission, $user_id)     {
        return Jf::$Rbac->check($permission, $user_id);
    }

    public function enforce($permission, $user_id) {
        return Jf::$Rbac->enforce($permission, $user_id);
    }

    public function reset($ensure = false) {
        return Jf::$Rbac->reset($ensure);
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
	
	DB::run("delete from t_rbac");
	DB::run("delete from sqlite_sequence where name='t_rbac'");
	
	$b = new Rbac("t_rbac");
	
	//$b->deleteNode(1);
	/*
	echo "\n", $b->getRoleId("guest");
	echo "\n", $b->getRoleId("editor");
	echo "\n", $b->getRoleId("editor/clips");
	echo "\n", $b->getRoleId("editor/survey");
	echo "\n", $b->getRoleId("editor/stats");
	echo "\n", $b->getRoleId("secretary");
	echo "\n", $b->getRoleId("secretary/liaison-officer");
	echo "\n", $b->getRoleId("staff");
	echo "\n", $b->getRoleId("staff/tecnical");
	echo "\n", $b->getRoleId("staff/tecnical/coach");
	echo "\n", $b->getRoleId("staff/tecnical/manager");
	echo "\n", $b->getRoleId("staff/tecnical/trainer");
	echo "\n", $b->getRoleId("staff/tecnical/referee");
	echo "\n", $b->getRoleId("staff/medical");
	echo "\n", $b->getRoleId("staff/medical/doctor");
	echo "\n", $b->getRoleId("staff/medical/physio");
	echo "\n", $b->getRoleId("staff/medical/terapist");
	echo "\n", $b->getRoleId("staff/logistic");
	echo "\n", $b->getRoleId("staff/marketing");
	echo "\n", $b->getRoleId("player/forward");
	echo "\n", $b->getRoleId("player/back");
	echo "\n", $b->getRoleId("player/leader");
	
	*/
	
	
	$b->setRole2Perm("guest"			,"page/clip/read");
	$b->setRole2Perm("player"		,"page/clip/read");
	$b->setRole2Perm("staff"			,"page/clip/read");
	$b->setRole2Perm("editor"		,"page/clip/read");

	$b->setRole2Perm("player"		,"page/clip/comments/read"); 
	$b->setRole2Perm("staff"			,"page/clip/comments/read"); 
	$b->setRole2Perm("editor"		,"page/clip/comments/read");
	
	$b->setRole2Perm("staff/tecnical/coach"		,"page/clip/comments/delete"); 
	$b->setRole2Perm("staff/tecnical/referee"	,"page/clip/comments/edit");
	$b->setRole2Perm("staff/medical/doctor"		,"page/clip/comments/edit");
	$b->setRole2Perm("editor/clip"				,"page/clip");
	
	$b->setUser2Role(65				,"staff/tecnical/coach");
	$b->setUser2Role(65				,"editor/clip"); 
	$b->setUser2Role(66				,"staff/tecnical"); 
	
	
	
	//for($i=45;$i<90;$i++) $l->deleteNode($i);
	
		$r = DB::run("SELECT n.id,GROUP_CONCAT(p.label,'/') AS path
    FROM t_rbac AS n,t_rbac AS p WHERE n.lft BETWEEN p.lft AND p.rht	
    GROUP BY n.id ORDER BY path")->fetchAll();
	foreach($r as $rr) echo sprintf("\n%2d -> %s",$rr['id'],$rr['path'] );


	echo "</pre>";
} 
catch (PDOException $e) {echo ($e->getMessage()); }
catch (Exception $e) {echo ($e->getMessage()); }
    

?>
<pre>

	SELECT COUNT(*) AS Result
	FROM
	userroles AS TUrel
	JOIN roles AS TRdirect ON (TRdirect.ID=TUrel.RoleID)
	JOIN roles AS TR ON ( TR.Lft BETWEEN TRdirect.Lft AND TRdirect.Rght)
	JOIN
	(	permissions AS TPdirect
	JOIN permissions AS TP ON ( TPdirect.Lft BETWEEN TP.Lft AND TP.Rght)
	JOIN rolepermissions AS TRel ON (TP.ID=TRel.PermissionID)
	) AS Temp ON ( TR.ID = Temp.RoleID)
	WHERE TUrel.UserID=?
	AND	Temp.ID=?,
	
	
	
	$UserID, $PermissionID

-- tutti i ruoli	
select n.*
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht
AND p.pid=1 and p.label='roles'

--tutti i permessi
select n.*
FROM t_rbac AS n,t_rbac AS p 
WHERE n.lft BETWEEN p.lft AND p.rht
AND p.pid=1 and p.label='permissions'	

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



