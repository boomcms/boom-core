<?php

namespace BoomCMS\Foundation\Http;

use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

trait ValidatesAssetUpload
{
    protected function validateAssetUpload(Request $request)
    {
        $validFiles = $errors = [];

        foreach ($request->file() as $files) {
            foreach ($files as $file) {
                if (!$file->isValid()) {
                    $errors[] = $file->getErrorMessage();

                    continue;
                }

                $type = AssetHelper::typeFromMimetype($file->getMimetype());

                if ($type === null) {
                    $errors[] = Lang::get('boomcms::asset.unsupported', [
                        'filename' =>  $file->getClientOriginalName(),
                        'mimetype' => $file->getMimetype(),
                    ]);

                    continue;
                }

                $validFiles[] = $file;
            }
        }

        return [$validFiles, $errors];
    }
}
