<?php

namespace BoomCMS\Core\Email;

use Email;
use Request;
use View;
use Boom\Person;
use Boom\Config;

class Newuser
{
    /**
	 *
	 * @var string
	 */
    protected $password;

    /**
	 *
	 * @var Model_Person
	 */
    protected $person;

    /**
	 *
	 * @var Request
	 */
    protected $request;

    /**
	 *
	 * @var string
	 */
    protected $viewFilename = 'boom/email/newuser';

    public function __construct(Person\Person $person, $password, Request $request)
    {
        $this->person = $person;
        $this->password = $password;
        $this->request = $request;
    }

    protected function _get_content()
    {
        return View::factory($this->viewFilename, [
            'password' => $this->password,
            'person' => $this->person,
            'request' => $this->request,
            'site_name' => Config::get('site_name'),
        ]);
    }

    public function send()
    {
        $content = $this->_get_content()->render();
        $this->_send($content);
    }

    protected function _send($content)
    {
        Email::factory('CMS Account Created')
            ->to($this->person->getEmail())
            ->from(Config::get('support_email'))
            ->message(View::factory('boom/email', [
                'request' => $this->request,
                'content' => $content,
            ]), 'text/html')
            ->send();
    }
}
