<?php
    class SendUnitsToAce extends Controllers{

        private $permission;
    
        public function __construct()
        {
            parent::__construct();
            session_start();
            //session_regenerate_id(true);
    
        }

        public function sendToAce()
        {
            $data = $this->model->sendToAce();
            die('respuesta:'.dep($data));
        }
    }

?>