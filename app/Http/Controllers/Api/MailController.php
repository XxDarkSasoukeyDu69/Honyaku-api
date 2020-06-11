<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function receiveFrom() {
        $to_name = 'ALexandre';
        $to_email = 'alex02.cailler@gmail.com';
        $data = array('name'=>'Ogbonna Vitalis(sender_name)', 'body' => 'A test mail');
        Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
            $message->to($to_email, $to_name)
                ->subject('Laravel Test Mail');
            $message->from('honyakuca@gmail.com','Test Mail');
        });
    }
}
