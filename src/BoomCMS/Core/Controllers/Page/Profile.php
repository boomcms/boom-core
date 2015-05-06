<?php

namespace BoomCMS\Core\Controllerss\Page;

class Profile extends Boom\Controller
{
    public function before()
    {
        if ( ! $this->environment->isDevelopment()) {
            throw new HTTP_Exception_404();
        }

        parent::before();
    }

    public function show()
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
