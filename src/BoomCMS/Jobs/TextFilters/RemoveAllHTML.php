<?php

namespace BoomCMS\Jobs\TextFilters;

class RemoveAllHTML extends BaseTextFilter
{
    public function handle()
    {
        return \strip_tags($this->text);
    }
}
