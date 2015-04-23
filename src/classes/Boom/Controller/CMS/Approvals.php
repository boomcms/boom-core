<?php

namespace Boom\Controller\CMS;

use Boom\Page;

class Approvals extends Boom\Controller
{
    public function before()
    {
        parent::before();

        $this->authorization('manage_approvals');
    }

    public function action_index()
    {
        $this->template = new View('boom/approvals/index', [
            'pages' => $this->_get_pages_awaiting_approval(),
        ]);
    }

    protected function _get_pages_awaiting_approval()
    {
        $finder = new Page\Finder();
        $finder->addFilter(new Page\Finder\Filter\PendingApproval());

        return $finder->findAll();
    }
}
