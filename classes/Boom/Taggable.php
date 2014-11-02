<?php

namespace Boom;

interface Taggable
{
    public function addTagByName($name);
    public function removeTagByName($name);
}
