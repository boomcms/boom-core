<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package	Sledge/People
 * @category	Controllers
 * @author	Rob Taylor
 */
class Sledge_Controller_Cms_Auth extends Controller
{
	public function action_login()
	{
		require Kohana::find_file('vendor/openid', 'openid');

		if ( ! $this->request->query('openid_mode'))
		{
			$openid = new LightOpenID($_SERVER['HTTP_HOST']);
			$openid->identity = 'https://www.google.com/accounts/o8/id';
			$openid->required = array('contact/email', 'namePerson');

			$this->redirect($openid->authUrl());
		}
		else
		{
			$openid = new LightOpenID($_SERVER['HTTP_HOST']);
			if ($openid->validate())
			{
				$attrs = $openid->getAttributes();
				$person = new Model_Person(array('email' => $attrs['contact/email']));

				if ( ! $person->loaded())
				{
					die('invalid email');
				}

				if (isset($attrs['namePerson']) AND $person->name != $attrs['namePerson'])
				{
					// Update the person's name.
					$person->name = $attrs['namePerson'];
					$person->update();
				}

				Auth::instance()->login($person, " ");

				$this->redirect('/');
			}
			else
			{
				$this->redirect('/cms/login');
			}
		}
	}

	public function action_logout()
	{
		Auth::instance()->logout(TRUE);

		$this->redirect('/');
	}
} // End Auth