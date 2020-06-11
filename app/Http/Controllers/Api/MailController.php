<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\MailContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function receiveFrom(Request $request) {
        Mail::to('alex02.cailler@gmail.com')->send(new MailContact($request));

        return response([
            'message' => 'Email envoyé avec succès !',
            'status' => 200
        ]);
    }
}
