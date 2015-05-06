<?php

namespace BoomCMS\Core\Controllers\CMS;

class Profile extends Boom\Controller
{
    public function action_view()
    {
        $v = new View('boom/account/profile', [
            'person' => $this->person,
            'auth' => $this->auth,
            'logs' => [],
            //'logs' => $this->person->get_recent_account_activity(),
        ]);

        $this->response->body($v);
    }

    public function action_save()
    {
        extract($this->request->input());

        $name && $this->person->setName($name);

        if ($new_password && $new_password != $current_password) {
            if ( ! $this->person->getPassword() || $this->auth->check_password($current_password)) {
                $this->person->setEncryptedPassword($this->auth->hash($new_password));

                $v = new View('boom/account/profile', [
                    'person' => $this->person,
                    'auth' => $this->auth,
                    'logs' => [],
                    'message' => 'Password updated'
                ]);

            } else {
                $v = new View('boom/account/profile', [
                    'person' => $this->person,
                    'auth' => $this->auth,
                    'logs' => [],
                    'message' => 'Invalid password'
                ]);
            }
        }

        $this->person->save();
        $this->response->body($v);
    }
}
