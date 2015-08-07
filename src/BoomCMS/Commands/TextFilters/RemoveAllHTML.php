<?php

namespace BoomCMS\Commands\TextFilters;

class RemoveAllHTML extends BaseTextFilter
{
    public function handle()
    {
        return \strip_tags($this->text);
    }
}
