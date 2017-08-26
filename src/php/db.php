<?php
class DBOperator {
	private $dbh;
	private $dbn;
	private $u;
	private $p;
	function __construct($dbhost, $dbname, $user, $pass) {
		$this->dbh=$dbhost;
	    $this->dbn=$dbname;
	    $this->u=$user;
	    $this->p=$pass;
	}
	function parseSQL($t) {
		$rows=pg_num_rows($t);
		$items=array();
		for($i=0;$i<$rows;$i++) {
			array_push($items,pg_fetch_array($t,$i,PGSQL_ASSOC));
		}
		return $items;
	}
	function _getModsAsArray($offset=0, $quantity=40, $ver=true, $cat=true, $search=true) {
		$additional="";
		if(!is_bool($search)) {
			$additional=" WHERE tid SIMILAR TO '%".$search."%'";
		}
		if(!is_bool($ver)) {
			$additional.=" WHERE ver SIMILAR TO '%".$ver."%'";
		}
		if(!is_bool($cat)) {
			$additional.=" WHERE cat SIMILAR TO '%".$cat."%'";
		}
		if(!is_bool($ver)&&!is_bool($cat)) {
			$additional=" WHERE ver SIMILAR TO '".$ver."' AND cat SIMILAR TO '%".$cat."%'";
		}
		$connection=pg_connect("host=".$this->dbh." dbname=".$this->dbn." user=".$this->u." password=".$this->p);
 		$table=pg_query("SELECT * FROM xlistedshop_itms {$additional} ORDER BY updated DESC LIMIT {$quantity} OFFSET {$offset}");
		return $this->parseSQL($table);
	}
	function _getCommentsAsArray($to,$offset=0,$quantity=30) {
		$connection=pg_connect("host=".$this->dbh." dbname=".$this->dbn." user=".$this->u." password=".$this->p);
 		$table=pg_query("SELECT * FROM xlistedshop_comments WHERE \"to\"={$to} ORDER BY since DESC LIMIT {$quantity} OFFSET {$offset}");
		return $this->parseSQL($table);
	}
	function _getModById($id) {
		$connection=pg_connect("host=".$this->dbh." dbname=".$this->dbn." user=".$this->u." password=".$this->p);
		$mod=pg_query("SELECT * FROM xlistedshop_itms WHERE id=".$id);
		return pg_fetch_array($mod,null,PGSQL_ASSOC);
	}
	function _getUser($name="default") {
		$connection=pg_connect("host=".$this->dbh." dbname=".$this->dbn." user=".$this->u." password=".$this->p);
		return pg_fetch_array(pg_query("SELECT * FROM xlistedshop_users WHERE name='".$name."'"), NULL, PGSQL_ASSOC);
	}
	function _statistics() {
		$connection=pg_connect("host=".$this->dbh." dbname=".$this->dbn." user=".$this->u." password=".$this->p);
		return array("mods"=>pg_num_rows(pg_query("SELECT * FROM xlistedshop_itms")),"users"=>pg_num_rows(pg_query("SELECT * FROM xlistedshop_users")),"comments"=>pg_num_rows(pg_query("SELECT * FROM xlistedshop_comments")));
	}
	function _addItem($itemName,$xcategory,$xver) {
		$connection=pg_connect("host=".$this->dbh." dbname=".$this->dbn." user=".$this->u." password=".$this->p);
		$id=intval(pg_fetch_array(pg_query("SELECT COUNT(id) FROM xlistedshop_itms"), NULL, PGSQL_ASSOC)["count"]);
		$push=pg_query("INSERT INTO xlistedshop_itms VALUES('".$itemName."',".$id.",'admin',0.0,0.0,null,'".time()."','".time()."','".$xcategory."','".$xver."');");
		if(!$push){return false;}
		if(!mkdir('fores/dl/'.$id,0777,true)){return false;}
		$desc=fopen("fores/descs/".$id.".xml","c+");
		fwrite($desc,$_GET['desc']);
		return $itemName;
	}
	function _addComment($from,$text,$to,$since) {
		$connection=pg_connect("host=".$this->dbh." dbname=".$this->dbn." user=".$this->u." password=".$this->p);
		$id=intval(pg_fetch_array(pg_query("SELECT COUNT(uuid) FROM xlistedshop_comments"), NULL, PGSQL_ASSOC)["count"]);
		$push=pg_query("INSERT INTO xlistedshop_comments VALUES({$to},'{$text}',{$since},{$id},0.0,'{$from}');");
		if(!$push){return false;}
		return $id;
	}
}
?>