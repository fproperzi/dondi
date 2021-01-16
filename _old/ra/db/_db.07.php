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
		echo "<br>run=$sql";
		$stmt = self::instance()->prepare($sql);
		$stmt->execute($args);
		return $stmt;
	}
	public function transStart() {
		if($this->trans) return;
		$this->trans = true;
		$this->instance()->beginTransaction();
	}

	public function transEnd() {
		$this->trans = false;
		$this->instance()->commit();
	}
}
//https://eval.in/779206
//https://eval.in/779208
class NestedSet extends DB {
	public $table = null; 
	public $label = null;
	public $id	= null; 
	
	// pid,lft,rht,lvl   must!
	  
	function __construct($table,$label,$id) {
		$this->table = $table;
		$this->label = $label;
		$this->id	= $id;
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
		return $this->run("SELECT {$this->id}, {$this->label}, lvl, pid,
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
	public function addNode($label = "", $pid = "") {

		if($pid == "")				 // if no parent define, add to root node
			$pid = $this->addRoot();   // root add or select if exist 
		
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
	
	// Move existing node1 into node 2
	public function addChild($node_id_1, $node_id_2) {
		if($node_id_1 == $node_id_2)  
			return false;	//same node
		
		$r = $this->getRows("{$this->id} in (?,?)", array($node_id_1, $node_id_2));

		if(count($r) != 2) 
			return false;	//no node	   

		if($r[0][$this->id] == $node_id_1) list($node1,$node2) = $r;
		else							   list($node2,$node1) = $r;
	
		$node1_size = $node1['rht'] - $node1['lft'] + 1;

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
		if($node_id_1 == $node_id_2)  
			return false;	//same node
		
		$r = $this->getRows("{$this->id} in (?,?)", array($node_id_1, $node_id_2));
		if(count($r) != 2) 
			return false;	//no node	   

		if($r[0][$this->id] == $node_id_1) list($node1,$node2) = $r;
		else							   list($node2,$node1) = $r;

		$this->transStart();

		// if not in same level, put it in same level
		if($node1['lvl'] != $node2['lvl'] || $node1['pid'] != $node2['pid']) {
			$this->addChild($node_id_1, $node2['pid']);
			return $this->addBefore($node_id_1, $node_id_2);
		}

		// same level, put node 1 before node 2
		$node1_size = $node1['rht'] - $node1['lft'] + 1;
		$node2_size = $node2['rht'] - $node1['lft'] + 1;

		// temporary "remove" moving node
		$this->run("UPDATE {$this->table} SET lft = 0 - lft,rht = 0 - rht WHERE lft >= ? AND rht <= ?", array($node1['lft'], $node1['rht']));
		
		if($node1['lft'] > $node2['lft']) {	//move left
			//shift the node to right to give some room
			$this->run("UPDATE {$this->table} SET lft = lft + ?,rht = rht + ? WHERE lft >= ? AND rht <= ?'", array($node1_size, $node1_size, $node2['lft'], $node1['lft']));
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
		if($node_id_1 == $node_id_2) 
			return false;	//same node

		$r = $this->getRows("{$this->id} in (?,?)", array($node_id_1, $node_id_2));
		if(count($r) != 2) 
			return false;	//no node	   

		if($r[0][$this->id] == $node_id_1) list($node1,$node2) = $r;
		else							   list($node2,$node1) = $r;

		$this->transStart();

		// if not in same level, put it in same level
		if($node1['lvl'] != $node2['lvl'] || $node1['pid'] != $node2['pid']) {
			$this->addChild($node_id_1, $node2['pid']);
			return $this->addAfter($node_id_1, $node_id_2);
		}

		// same level, put node 1 before node 2
		$node1_size = $node1['rht'] - $node1['lft'] + 1;
		$node2_size = $node2['rht'] - $node1['lft'] + 1;

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
}
try { 


	$l = new NestedSet("t_lookup","lkp_name","lkp_id");
	$r = $l->getRow("lvl=0");  //("lvl=?",[0]);
	echo "<br>";var_dump($r);
	$r = $l->addNode("ciccio".time(), $pid = '');
	echo "<br>";var_dump($r);
	$r = $l->selectAll();
	echo "<br><pre>",print_r($r,true),"</pre>";  

} 
catch (PDOException $e) {print_r($e->getMessage()); }
catch (Exception $e) {print_r($e->getMessage()); }

