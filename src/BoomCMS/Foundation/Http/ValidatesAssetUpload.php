<?php

namespace BoomCMS\Foundation\Http;

use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Http\Request;

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
                    $errors[] = "File {$file->getClientOriginalName()} is of an unsuported type: {$file->getMimetype()}";
                    continue;
                }

                $validFiles[] = $file;
            }
        }

        return [$validFiles, $errors];
    }
}
