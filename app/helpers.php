<?php

    // Generate verification code in 6 character
    function generateVerificationCode() {
        return mt_rand(100000, 999999);
    }

    // Sending sms methods
    function sendVerificationCode($phone, $verificationCode) {

    }

    // resend function every 1 minutes
    function resend($data){
        if($data){
            $createdAt = $data->created_at;
            $seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($createdAt);
            $days    = floor($seconds / 86400);
            $hours   = floor(($seconds - ($days * 86400)) / 3600);
            if(floor(($seconds - ($days * 86400) - ($hours * 3600))/60) >= 1){
                $data->delete();
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

?>
