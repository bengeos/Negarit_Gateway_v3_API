<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $Negarit_Web_API_URL;
    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->Negarit_Web_API_URL = "https://api.negarit.net/api/"; /// Main Production Server
//        $this->Negarit_Web_API_URL = "http://127.0.0.1:8000/api/"; /// Local Server
    }

    public function sendPostRequestTooNegarit($request_route, $send_post_data){
        $url = $this->Negarit_Web_API_URL.$request_route;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $send_post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    public function sendGetRequestToNegarit($request_route){
        $url = $this->Negarit_Web_API_URL.$request_route;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
