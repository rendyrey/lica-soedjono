<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;

class MailController extends Controller
{
    public function send()
    {
        Mail::to('rendyreynaldy@gmail.com')->send(new SendMail);
    }
}
