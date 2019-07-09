<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Support\Facades\Settings as SettingsFacade;
use Illuminate\Auth\AuthManager as Auth;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class Support extends Controller
{
    public function getIndex()
    {
        return view('boomcms::support.form');
    }

    public function postIndex(Auth $auth, Request $request)
    {
        $email = SettingsFacade::get('site.support.email');
        $person = $auth->user();

        if (!$email) {
            return;
        }

        Mail::send('boomcms::email.support', [
            'request' => $request,
            'person'  => $person,
        ], function (Message $message) use ($email, $person, $request) {
            $message
                ->to($email)
                ->from($email, SettingsFacade::get('site.name'))
                ->replyTo($person->getEmail())
                ->subject($request->input('subject'));
        });


        //auto respond
        Mail::send('boomcms::email.support-auto-respond', [
            'request' => $request,
            'person'  => $person,
        ], function (Message $message) use ($email, $person, $request) {
            $message
                ->to($person->getEmail())
                ->from($email, SettingsFacade::get('site.name'))
                ->subject('Re: '.$request->input('subject'));
        });
    }
}
