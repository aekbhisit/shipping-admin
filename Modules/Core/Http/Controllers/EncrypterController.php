<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\Core\Entities\Districts ;

class EncrypterController extends Controller
{
    public function encrypter($string, $action='encrypt') {
        $key = "IhooiWErsdfoii7899h0phWEEerwhlkweJZkhoy097";
        $salt = "qydB2A6XtUo689joioiMVB5UIUIKHIo2881HKeihbdd" ;

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = $key;
        $secret_iv = $salt;
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    
}
