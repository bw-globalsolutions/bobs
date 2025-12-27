<?php
    class Validators{
        const regEx = [
            "email"     =>  "/^[^@]+@[^@]+\.[a-zA-Z]{2,}$/",
            "password"  =>  "/^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{12,32}$/"
        ];

        private function validRex($case, ...$value){
            foreach ($value as $item){
                if(!preg_match(self::regEx[$case], $item))
                    return false;
            }
            return true;
        }
        
        public static function check($cad){
            foreach ($cad as $key => $value){
                $aux = true;
                if(is_array($value)){
                    $aux = self::validRex($key, ...$value);
                } else{
                    $aux = self::validRex($key, $value);
                }
                if(!$aux) return false;
            }
            return true;
        }

        public static function getRegEx($regEx){
            return trim(self::regEx[$regEx], '/');
        }
    }