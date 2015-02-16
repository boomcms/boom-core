<?php

/**
* We use a 3rd party Kohana module to handle mptt trees.
* @link https://github.com/evopix/orm-mptt
*
*
*/
class Model_Page_MPTT extends \ORM_MPTT
{
    protected $table = 'page_mptt';
    protected $_belongs_to = ['page' => ['foreign_key' => 'id']];
    protected $_table_columns = [
        'id'            =>    '',
        'lft'            =>    '',
        'rgt'            =>    '',
        'parent_id'    =>    '',
        'lvl'            =>    '',
        'scope'        =>    '',
    ];
    protected $_reload_on_wakeup = true;
}
