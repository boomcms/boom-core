<?php

namespace Boom\Exception\Handler;

use Exception;
use Response;
use Kohana_Exception;
use View;
use Boom\Page;
use Kohana;
use Request;

/**
 * Exception handler which doesn't output any debugging information
 */
class Handler extends Handler
{
    public function execute()
    {
        parent::execute();

        try {
            $this->_execute();
        } catch (Exception $e) {
            echo Response::factory()
                ->status($this->code)
                ->headers('Content-Type', 'text/plain')
                ->send_headers()
                ->body(Kohana_Exception::text($e));

            exit(1);
        }
    }

    protected function _execute()
    {
        $page = $this->findErrorPage($this->code);

        if ($page->loaded()) {
            $body = $this->getPageContent($page);
        } elseif (Kohana::find_file('views', "boom/errors/$this->code")) {
            $body = $this->getDefaultError($this->code);
        } else {
            echo Kohana_Exception::response($this->e)->send_headers()->body();
            exit(1);
        }

        echo $this->getResponse($body)->send_headers()->body();
        exit(1);
    }

    protected function findErrorPage($code)
    {
        return Page\Factory::byInternalName($code);
    }

    protected function getDefaultError($code)
    {
        return View::factory("boom/errors/$code")->render();
    }

    protected function getPageContent(Page\Page $page)
    {
        return Request::factory($page->url())
            ->execute()
            ->body();
    }

    protected function getResponse($body)
    {
        return Response::factory()
            ->status($this->code)
            ->headers('Content-Type', 'text/html')
            ->body($body);
    }
}
