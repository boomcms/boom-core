<?php

/**
 * Exception handler which doesn't output any debugging information
 */
class Boom_Boom_Exception_Handler_Private extends Boom_Exception_Handler
{
    public function execute()
    {
        parent::execute();

        try {
            $this->_execute();
        } catch (Exception $e) {
            echo Response::factory()
                ->status($this->_code)
                ->headers('Content-Type', 'text/plain')
                ->send_headers()
                ->body(Kohana_Exception::text($e));

            exit(1);
        }
    }

    protected function _execute()
    {
        $page = $this->_find_error_page($this->_code);

        if ($page->loaded()) {
            $body = $this->_get_page_content($page);
        } elseif (Kohana::find_file('views', "boom/errors/$this->_code")) {
            $body = $this->_get_default_error($this->_code);
        } else {
            echo Kohana_Exception::response($this->_e)->send_headers()->body();
            exit(1);
        }

        echo $this->_get_response($body)->send_headers()->body();
        exit(1);
    }

    protected function _find_error_page($code)
    {
        return \Boom\Page\Factory::byInternalName($code);
    }

    protected function _get_default_error($code)
    {
        return View::factory("boom/errors/$code")->render();
    }

    protected function _get_page_content(Model_Page $page)
    {
        return Request::factory($page->url())
            ->execute()
            ->body();
    }

    protected function _get_response($body)
    {
        return Response::factory()
            ->status($this->_code)
            ->headers('Content-Type', 'text/html')
            ->body($body);
    }
}
