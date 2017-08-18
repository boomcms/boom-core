<?php

namespace BoomCMS\Foundation\Http;

use BoomCMS\Support\Helpers\Asset as AssetHelper;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Lang;

trait ValidatesAssetUpload
{
    protected function validateAssetUpload(Request $request)
    {
        $validFiles = $errors = [];

        foreach ($request->file() as $files) {
            foreach ($files as $file) {
                $error = $this->validateFile($file);

                $error === true ? ($validFiles[] = $file) : ($errors[] = $error);
            }
        }

        return [$validFiles, $errors];
    }

    protected function validateFile(UploadedFile $file)
    {
        if (!$file->isValid()) {
            return $file->getErrorMessage();
        }

        $type = AssetHelper::typeFromMimetype($file->getMimetype());

        if ($type === null) {
            return Lang::get('boomcms::asset.unsupported', [
                'filename' => $file->getClientOriginalName(),
                'mimetype' => $file->getMimetype(),
            ]);
        }

        return true;
    }
}
