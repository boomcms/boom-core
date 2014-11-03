<?php

use Boom\Config;

class Controller_Cms_Auth_Recover extends Controller_Cms_Auth
{
    public function action_create_token()
    {
        $person = new Model_Person(['email' => $this->request->post('email')]);

        if ( ! $person->loaded() || ! $person->enabled) {
            $this->_display_form(['error' => Kohana::message('auth', 'recover.errors.invalid_email')]);

            return;
        }

        $token = ORM::factory('PasswordToken')
            ->values([
                'person_id' => $person->id,
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
            ->to($person->email)
            ->from(Config::get('support_email'))
            ->message(new View('boom/email', [
                'content' => $email_body,
                'request' => $this->request,
            ]), 'text/html')
            ->send();

        $this->response->body(new View('boom/account/recover/email_sent'));
    }

    public function action_show_form()
    {
        $this->_display_form();
    }

    public function action_set_password()
    {
        $token = new Model_PasswordToken(['token' => $this->request->query('token')]);

        if ( ! $token->loaded() || $token->is_expired()) {
            if ($token->is_expired()) {
                $token->delete();
            }

            $this->_display_form(['error' => Kohana::message('auth', 'recover.errors.invalid_token')]);

            return;
        }

        if ($this->request->post('password1') && $this->request->post('password2')) {
            if ( ! Security::check($this->request->post('csrf'))) {
                throw new HTTP_Exception_500();
            }

            if ($this->request->post('password1') != $this->request->post('password2')) {
                $this->_display_form(['error' => Kohana::message('auth', 'recover.errors.password_mismatch')]);

                return;
            }

            $hashed_password = $this->auth->hash($this->request->post('password1'));

            $token->person
                ->set('password', $hashed_password)
                ->update();

            DB::delete('password_tokens')
                ->where('person_id', '=', $token->person->id)
                ->execute();

            $this->auth->force_login($token->person);
            $this->redirect('/');
        } else {
            $this->_display_form(['token' => $token, 'email' => $token->person->email]);
        }
    }

    protected function _display_form($vars = [])
    {
        $this->response->body(new View('boom/account/recover/form', $vars));
    }
}
