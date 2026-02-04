<?php
class RoundModel extends Mysql {
	
	public function __contruct(){
		parent::__construct();

	}
	
	public function getRound($columns=[], $condition=NULL){
				
		$query = "SELECT ". (count($columns) ? implode(',', $columns) : "*") ." 
				  FROM round  
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getRoundsIds($condition=NULL){
				
		$query = "SELECT GROUP_CONCAT(id) as ids
				  FROM round  
				  ". ($condition ? " WHERE $condition " : '') ." 
				  ORDER BY id ASC";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request[0]['ids'];
	}
	
	public function insertRound($args){

		//query y values de argumentos
		$query = "INSERT INTO round SET ";
		$values = [];
		foreach($args as $key => $val){
			$query .= "`$key` = ?, ";
			$values[] = $val;
		}
		$query = substr($query, 0, -2);
		
		//var_dump($args);
		$res = new Mysql;
		$request = $res -> insert($query, $values);
		
		return $request;
	}	

	public function getAllRounds(){
		$query = "SELECT r.id, b.prefix, r.name, r.type, c.name country FROM round r LEFT JOIN brand b ON (b.id = r.brand_id) LEFT JOIN country c ON (c.id = r.country_id)";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}

	public function getRoundAudit($id){
		$query = "SELECT round_id FROM audit WHERE id = $id";
		//echo $query;
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request[0]['round_id'];
	}

	public function getRoundInfo($periodoAConsiderar, $roundN){
	
		$tmpstr = explode("-", $periodoAConsiderar);
		
		if((int)$tmpstr[1]>=1){
			$nmR = "1";
			$sD = (int)$tmpstr[0]."-01-01 00:00:00";
			$eD = (int)$tmpstr[0]."-03-31 23:59:59";
		
		} 
		
		if((int)$tmpstr[1]>=4){
			$nmR = "2";
			$sD = (int)$tmpstr[0]."-04-01 00:00:00";
			$eD = (int)$tmpstr[0]."-06-30 23:59:59";
		}

		if((int)$tmpstr[1]>=7){
			$nmR = "3";
			$sD = (int)$tmpstr[0]."-07-01 00:00:00";
			$eD = (int)$tmpstr[0]."-09-30 23:59:59";
		}

		if((int)$tmpstr[1]>=10){
			$nmR = "4";
			$sD = (int)$tmpstr[0]."-10-01 00:00:00";
			$eD = (int)$tmpstr[0]."-12-31 23:59:59";
		}

		if($roundN) $nmR = $roundN;
		$month = date('F', strtotime("$periodoAConsiderar-01"));
		
		return array('nm' => "Round $nmR $tmpstr[0]",
					'desde' => $sD,
					'hasta' => $eD,
					'year' => $tmpstr[0],
					'month' => $month);
		
	}

	public function getRoundAce($tipo, $name){
		$query = "SELECT id id_round, name round, type
            FROM round
            WHERE type = (
                        CASE " . $tipo . "
                        WHEN 1 THEN 'Standard'
                        WHEN 11 THEN 'Re-Audit'
						WHEN 18 THEN 'Re auditoria 2da.'
                        WHEN 23 THEN 'Calibration Audit'
						WHEN 28 THEN 'Re auditoria 3ra.'
                        WHEN 29 THEN 'Re auditoria 4ta.'
                        END
                    )
                AND name = '" . $name . "'
            ORDER BY id_round DESC
            LIMIT 1";
		$res = new Mysql;
		$request = $res -> select_all($query);
		
		return $request;
	}
}
?>