<?php

class EmailValidatorModel extends Mysql {

	public function __construct(){
		parent::__construct();
	}

	public function save(String $email, bool $is_valid, String $jsonRequest)
	{
		$query_exist = "SELECT email FROM email_validator WHERE email = '$email'";
		$select = $this->select($query_exist);
		$date = date("Y-m-d");
		if (empty($select)) {
			$query_insert = "INSERT INTO email_validator(email, is_valid, validation_date, last_json_request) 
							VALUES (?,?,?,?) ";
			$arrData = array($email, $is_valid? 1 : 0, $date, $jsonRequest);
			$request = $this->insert($query_insert, $arrData);
		} else {
			$sql = "UPDATE email_validator SET is_valid = ?, validation_date = ?, last_json_request = ? WHERE email = '$email'";
            $request = $this->update($sql, [$is_valid? 1 : 0, $date, $jsonRequest]);
		}
		return $request;
	}

	public function need_validate_email(String $email)
	{
		$date_now = date("d-m-Y");
		$dateFilter = strtotime('-'.DAYS_VALIDATE_EMAIL.' day', strtotime($date_now));
		$dateFilter = date('Y-m-d', $dateFilter);
		$query_select = "SELECT email FROM email_validator WHERE email = '$email' and validation_date < '$dateFilter'";
		$request = $this->select_all($query_select);
		$count = count($request);
		return $count > 0;
	}

	public function is_valid_in_database($email)
	{
		$query_select = "SELECT email FROM email_validator WHERE email = '$email' and is_valid = 1";
		$request = $this->select_all($query_select);
		$count = count($request);
		return $count > 0;
	}

	public function exist_in_database($email) 
	{
		$query_select = "SELECT email FROM email_validator WHERE email = '$email'";
		$request = $this->select_all($query_select);
		$count = count($request);
		return $count > 0;
	}
}
?>