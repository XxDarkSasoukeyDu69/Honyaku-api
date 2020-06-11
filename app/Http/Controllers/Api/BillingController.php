<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function postPaymentIntent(Request $request) {

        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'http://localhost:3001/create-payment-intent');
        $responseString = $response->getBody();
        $responseJson = json_decode($responseString);

        return response()->json(['countries_code' =>  $responseJson]);
    }
}
