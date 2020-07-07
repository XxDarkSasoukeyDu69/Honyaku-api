<?php

namespace App\Http\Controllers\Api;

use App\File;
use App\Http\Controllers\Controller;
use App\Mail\MailFileTranslateFinished;
use App\Mail\MailOrderAccept;
use Illuminate\Http\Request;
use App\Http\Controllers\Api;
use Illuminate\Support\Facades\Mail;

class StripeController extends Controller
{

    public function successPayment(Request $request) {

        $validatedData = $request->validate([
            'file_id' => 'required',
        ]);

        $file = File::find($validatedData['file_id']);

        $test = (new GoogleController)->translate($file['contentToTranslate'], $file['sourceLang'], $file['targetLang']);

        lad($test[0]);

        Mail::to($file['fileMail'])->send(new MailFileTranslateFinished("", $test[0]->{'translatedText'}, $file,  'txt'));

        return response()->json(['status' =>  "ok", 'googleTrad' => $test]);
    }

    public function insertBilling(Request $request) {

        $validatedData = $request->validate([
            'fileName'      => 'required',
            'fileMail'      => 'required',
            'file'          => 'required',
            'targetLang'    => 'required',
            'sourceLang'    => 'required',
            'fileType'      => 'required',
        ]);

        $content = '';
        if ($validatedData['fileType'] === "json") {

            $content = (new FileController)->loop(json_decode(file_get_contents($validatedData['file']),JSON_PRETTY_PRINT), "");

        } else if ($validatedData['fileType'] === "docx") {

            $content = (new FileController)->convertDocxToText($validatedData['file']);

        } else {

            $content = file_get_contents($validatedData['file']);

        }

        $validatedData['contentToTranslate'] = $content;
        $validatedData['charNbr'] = strlen($content);
        $validatedData['price'] = $this->calculateOrderAmount(strlen($content));
        $validatedData['automatic'] = true;

        return response()->json(['data' => File::create($validatedData)]);

    }

    public function createPaymentIntent(Request $request) {

        $validatedData = $request->validate([
            'file_id' => 'required',
        ]);

        $file = File::find($validatedData['file_id']);

        \Stripe\Stripe::setApiKey(''.env('STRIPE_KEY').'');
        $amount = $this->calculateOrderAmount(strlen($file->contentToTranslate));
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'eur',
            'description' => "traduction automatique",
            'metadata' => ['integration_check' => 'accept_a_payment'],
        ]);

        return response()->json([
            'publishable_key' => env('STRIPE_KEY'),
            'client_secret' => $paymentIntent->client_secret,
            'file_id' => $file->id,
            'amount' => $amount,
            'nbrChar' => strlen($file->contentToTranslate)
        ]);
    }

    function calculateOrderAmount($nrbChar) {
        // 5000 = 199
        $amount = 199;
        if($nrbChar <= 5000) {
            $amount = 199;
        } else {
            $amount = (round((($nrbChar) / 7500) * 199, 1) * 100) - 199;
        }
        return $amount;
    }
}
