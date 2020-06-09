<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function getLanguageSupport() {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://translation.googleapis.com/language/translate/v2/languages?key=' . env('GOOGLE_TRANSLATE_KEY'));
        $responseString = $response->getBody();
        $responseJson = json_decode($responseString);

        return response()->json(['countries_code' =>  $responseJson->{'data'}->{'languages'}]);
    }
}
