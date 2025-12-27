<?php

class EmailValidator extends Controllers {

    public function test_validation(){
        $request = $this->validate_email($_POST['email'], true);
        die(json_encode(['is_valid' => $request]));
    }

    private function get_api_validation($email) 
    {
        $api_url = 'https://emailvalidation.abstractapi.com/v1/?api_key='.API_KEY_ABSTRACT.'&email='.$email;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    private function verify_email_status_and_save_status($email) 
    {
        $requestEmailValid = $this->get_api_validation($email);
        $isEmailValid = json_decode($requestEmailValid)->is_smtp_valid->value;
        $this->model->save($email, $isEmailValid, $requestEmailValid);
        return $isEmailValid;
    }

    public function validate_email($emails, $force = false) {
        $emailArray = explode(",", $emails);
        $emailFiltered = array();
        foreach($emailArray as $email) {
            $email = trim($email);
            if(!$this->model->exist_in_database($email)) {
                if ($this->verify_email_status_and_save_status($email)) {
                    array_push($emailFiltered, $email);
                }
            } else {
                if ($this->model->is_valid_in_database($email)) {
                    if($this->model->need_validate_email($email) || $force) {
                        if ($this->verify_email_status_and_save_status($email)) {
                            array_push($emailFiltered, $email);
                        }
                    } else {
                        array_push($emailFiltered, $email);
                    }
                }
            }
        }
        return implode(",", $emailFiltered);
    }
}

?>