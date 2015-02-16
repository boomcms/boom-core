<?php

namespace Boom\Controller\CMS;

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
        extract($this->request->post());

        $name && $this->person->setName($name);

        if ($new_password && $new_password != $current_password) {
            if ( ! $this->person->getPasword() || $this->auth->checkPassword($current_password)) {
                $this->person->setEncryptedPassword($this->auth->hash($new_password));
            } else {
                $this->response
                    ->status(500)
                    ->headers('Content-Type', static::JSON_RESPONSE_MIME)
                    ->body(json_encode(['message' => 'Invalid password']));
            }
        }

        $this->person->save();
    }
}
