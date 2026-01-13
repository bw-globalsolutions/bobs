<?php
    class SendUnitsToAceModel extends Mysql {
	
        public function __construct(){
            parent::__construct();
        }

        public function sendToAce(){
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 0);

            //WHERE u.id_unit IN (SELECT lu.id_unit FROM log_units lu WHERE lu.send = 0 AND send_to = 'ace' )
            $sql = "SELECT DISTINCT u.id, u.name, u.number, u.address_1, 'Bobs' as brand, u.status FROM location u  LIMIT 280";

            $res = new Mysql;
            $units = $res->select_all($sql);

            if(empty($units)){
                return 'No records to send';
            }

            $data_to_send = [];
            foreach($units as $u){
                $data_to_send['stores'][] = [
                    'name'      => $u['name'],
                    'number'    => $u['number'],
                    'location'  => $u['address_1'],
                    'brand'     => $u['brand'],
                    'status'    => 'Active',
                    'country'   => 'BRA'
                ];
            }

            //die(dep($data_to_send));
            //stage:https://stage-ace.arguilea.com/api/store/batch prod:https://ace.arguilea.com/api/store/batch
            $url = 'https://stage-ace.arguilea.com/api/store/batch';
            //stage: QUMzX1NUT1JFX0tFWV9DT01QTEVURQ prod:94kmxirFiDnEEAZdAefcenjhGM44Pt
            $headers = [
                'Content-Type: application/json',
                'apiKey: QUMzX1NUT1JFX0tFWV9DT01QTEVURQ'
            ];

            if($pet = curl_init($url)){
                $data_to_send = json_encode($data_to_send, JSON_UNESCAPED_UNICODE);
        
                curl_setopt($pet, CURLOPT_POSTFIELDS, $data_to_send);
                curl_setopt($pet, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($pet, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($pet);
        
                if(curl_error($pet)){
                    $error_msg = curl_error($pet);
                    return $error_msg;
                } else { 
        
                    $result = json_decode($result, true);
                    $storeNumbers = [];
                    foreach ($result['stores'] as $store) {
                        if (in_array($store['original']['message'], ['Store updated', 'Store saved complete', 'Can´t convert direction'])) {
                            $storeNumbers[] = $store['original']['store']['number'];
                        }
                    }
        
                    if(!empty($storeNumbers)){
                        $storeNumbers = implode("','", $storeNumbers);
                        /*$sql = "UPDATE log_units SET send = 1 WHERE send_to = 'ace' AND id_unit IN (SELECT id FROM location WHERE number IN ('$storeNumbers'))";
                        db_query($sql);*/
                    }

                    return $result; 
                }
        
                curl_close($pet);
                
            } else  return 'curl_init() fail';

        }

    }
?>