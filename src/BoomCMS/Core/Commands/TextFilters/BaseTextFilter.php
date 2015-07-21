<?php

namespace BoomCMS\Core\Commands\TextFilters;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

abstract class BaseTextFilter extends Command implements SelfHandling
{
    protected $text;

    public function __construct($text)
    {
        $this->text = $text;
    }
}
