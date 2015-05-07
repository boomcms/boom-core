<?php

namespace BoomCMS\Core\Controllers\CMS;

class Profile extends Boom\Controller
{
    public function view()
    {
        return View::make('boom::account.profile', [
            'person' => $this->person,
            'auth' => $this->auth,
            'logs' => [],
            //'logs' => $this->person->get_recent_account_activity(),
        ]);
    }

    public function save()
    {
        extract($this->request->input());

        $name && $this->person->setName($name);

        if ($new_password && $new_password != $current_password) {
            if ( ! $this->person->getPassword() || $this->auth->check_password($current_password)) {
                $this->person->setEncryptedPassword($this->auth->hash($new_password));

                return View::make('boom::account.profile', [
                    'person' => $this->person,
                    'auth' => $this->auth,
                    'logs' => [],
                    'message' => 'Password updated'
                ]);

            } else {
                return View::make('boom::account.profile', [
                    'person' => $this->person,
                    'auth' => $this->auth,
                    'logs' => [],
                    'message' => 'Invalid password'
                ]);
            }
        }

        $this->person->save();
    }
}
