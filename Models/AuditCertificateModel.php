<?php
class AuditCertificateModel extends Mysql{

	public function __construct()
	{
		parent::__construct();
	}

    public function getPreviousAudit(int $lnumber, string $type, string $dvisit){
        $sql = "SELECT id, scoring_id FROM audit_list WHERE location_number = $lnumber AND type IN('$type') AND date_visit < '$dvisit' ORDER BY date_visit DESC LIMIT 2";
        $response = $this->select_all($sql);
        return $response;
    }

    public function getAuditListById($audit_id){	
		$query = "SELECT type, round_name, date_visit, date_visit_end, location_number, location_name, location_address, scoring_id, brand_name FROM audit_list a WHERE id=$audit_id ORDER BY date_visit DESC, id DESC";
		$request = $this->select($query);
		return $request;
	}

}
?>