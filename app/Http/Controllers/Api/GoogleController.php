<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function getLanguageSupport() {
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://translation.googleapis.com/language/translate/v2/languages?key=AIzaSyASRBU66AiGFr_EW4OK-pvwvg79wCDy63A' . env('GOOGLE_TRANSLATE_KEY'));
        $responseString = $response->getBody();
        $responseJson = json_decode($responseString);

        return response()->json(['countries_code' =>  $responseJson->{'data'}->{'languages'}]);
    }

    public function translate($content, $source, $target) {

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://translation.googleapis.com/language/translate/v2?key=AIzaSyASRBU66AiGFr_EW4OK-pvwvg79wCDy63A&q='. $content .'&target='. $target .'&source='. $source .'');
        $responseString = $response->getBody();
        $responseJson = json_decode($responseString);

        return $responseJson->{'data'}->{'translations'};

    }
}
