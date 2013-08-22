<?php

class Boom_Controller_Page_Profile extends Boom_Controller
{
	public function before()
	{
		if (Kohana::$environment != Kohana::DEVELOPMENT)
		{
			throw new HTTP_Exception_404;
		}
	}

	public function action_show()
	{
		Kohana::$profiling = TRUE;

		$original_state = $this->editor->state();
		$this->editor->state(Editor::DISABLED);

		$uri = str_replace('.profile', '', $this->request->uri());
		Request::factory($uri)->execute();

		$this->editor->state($original_state);

		$this->response->body(new View('profiler/stats'));
	}
}