<?php

class Boom_Controller_Page_Profile extends Boom_Controller
{
	public function before()
	{
		if (Kohana::$environment != Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}

		parent::before();
	}

	public function action_show()
	{
		Kohana::$profiling = true;

		$original_state = $this->editor->getState();
		$this->editor->setState(\Boom\Editor::DISABLED);

		$uri = str_replace('.profile', '', $this->request->uri());
		Request::factory($uri)->execute();

		$this->editor->setState($original_state);

		$this->response->body(new View('profiler/stats'));
	}
}