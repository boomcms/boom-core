<?php

namespace BoomCMS\Core\Controllers\CMS\Auth;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Controllers\Controller;

class Recover extends Controller
{
    public function createToken()
    {
		try {
			$token = $this->auth->createRecoverToken($this->request->input('email'));
		} catch (Auth\InvalidPersonException $e) {
            return $this->showForm([
				'error' => Lang::get('boom::recover.errors.invalid_email')
			]);
		}
        $person = $this->provider->findByEmail($this->request->input('email'));
		


        $token = ORM::factory('PasswordToken')
            ->values([
                'person_id' => $person->getId(),
                'token' => sha1(uniqid(null, true)),
                'expres' => $_SERVER['REQUEST_TIME'] + Date::HOUR
            ])
            ->create();

        $email_body = View::factory('boom/email/recovery', [
            'site_name' => Config::get('site_name'),
            'person' => $person,
            'token' => $token,
            'request' => $this->request,
        ]);

        Email::factory('CMS Password Reset')
            ->to($person->getEmail())
            ->from(Config::get('support_email'))
            ->message(new View('boom/email', [
                'content' => $email_body,
                'request' => $this->request,
            ]), 'text/html')
            ->send();

        $this->response->body(new View('boom/account/recover/email_sent'));
    }

    public function showForm($vars = [])
    {
        return View::make('boom::account.recover.form', $vars);
    }

    public function setPassword()
    {
        $token = new Model_PasswordToken(['token' => $this->request->query('token')]);

        if ( ! $token->loaded() || $token->is_expired()) {
            if ($token->is_expired()) {
                $token->delete();
            }

            $this->_display_form(['error' => Kohana::message('auth', 'recover.errors.invalid_token')]);

            return;
        }

        if ($this->request->input('password1') && $this->request->input('password2')) {
            if ( ! Security::check($this->request->input('csrf'))) {
                throw new HTTP_Exception_500();
            }

            if ($this->request->input('password1') != $this->request->input('password2')) {
                $this->_display_form(['error' => Kohana::message('auth', 'recover.errors.password_mismatch')]);

                return;
            }

            $hashed_password = $this->auth->hash($this->request->input('password1'));

            $token->person
                ->set('password', $hashed_password)
                ->update();

            DB::delete('password_tokens')
                ->where('person_id', '=', $token->getPerson()->getId())
                ->execute();

            $this->auth->force_login($token->getPerson());
            $this->redirect('/');
        } else {
            $this->_display_form(['token' => $token, 'email' => $token->getPerson()->getEmail()]);
        }
    }
}
