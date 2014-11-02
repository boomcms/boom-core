<?php

class Controller_Cms_Profile extends Boom\Controller
{
    public function action_view()
    {
        $v = new View('boom/account/profile', array(
            'person' => $this->person,
            'auth' => $this->auth,
            'logs' => $this->person->get_recent_account_activity(),
        ));

        $this->response->body($v);
    }

    public function action_save()
    {
        extract($this->request->post());

        $name && $this->person->set('name', $name);

        if ($new_password && $new_password != $current_password) {
            if ( ! $this->person->password || $this->auth->check_password($current_password)) {
                $this->person->set('password', $this->auth->hash($new_password));
            } else {
                $this->response
                    ->status(500)
                    ->headers('Content-Type', static::JSON_RESPONSE_MIME)
                    ->body(json_encode(array('message' => 'Invalid password')));
            }
        }

        $this->person->update();
    }
}
