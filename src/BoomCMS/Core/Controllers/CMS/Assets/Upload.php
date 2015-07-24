<?php

use BoomCMS\Core\Asset;

class Controller_Cms_Assets_Upload extends Controller_Cms_Assets
{
    public function replace()
    {
        $asset = new Model_Asset($this->request->input('asset_id'));

        $filename = Arr::pluck($_FILES, 'tmp_name');
        $filename = $filename[0][0];

        $asset->replace_with_file($filename);

        return [$asset->getId()];
    }
}
