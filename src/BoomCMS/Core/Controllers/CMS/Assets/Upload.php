<?php

use \Boom\Asset;
use Boom\Exception;

class Controller_Cms_Assets_Upload extends Controller_Cms_Assets
{
    /**
	 * Create assets from a file upload.
	 *
	 * @uses Model_Asset::create_from_file()
	 *
	 */
    public function action_process()
    {
        $asset_ids = $errors = [];
        $now = new DateTime('now');

        $this->response->headers('Content-Type', static::JSON_RESPONSE_MIME);

        foreach ( (array) $_FILES as $files) {
            foreach ( (array) $files['tmp_name'] as $i => $filename) {
                try {
                    $mime = Asset\Mimetype::factory(File::mime($filename));
                } catch (Exception\UnsupportedMimeType $e) {
                    $errors[] = "File {$files['name'][$i]} is of an unsuported type: {$e->getMimetype()}";
                    continue;
                }

                $asset = Asset\Factory::createFromType($mime->getType());
                $asset
                    ->setUploadedBy($this->person)
                    ->setVisibleFrom($now)
                    ->setLastModified($now)
                    ->setTitle(pathinfo($files['name'][$i], PATHINFO_FILENAME))
                    ->setFilename($files['name'][$i])
                    ->setFilesize(filesize($filename));

                if ($asset instanceof Asset\Type\Image) {
                    list($width, $height) = getimagesize($filename);

                    $asset
                        ->setWidth($width)
                        ->setHeight($height);
                }

                $asset_ids[] = $asset->save()->getId();

                move_uploaded_file($filename, Asset\Asset::directory() . DIRECTORY_SEPARATOR . $asset->getId());
            }

            if (count($errors)) {
                $this->response
                    ->status(500)
                    ->body(json_encode($errors));
            } else {
                $this->response
                    ->body(json_encode($asset_ids));
            }
        }
    }

    public function action_replace()
    {
        $asset = new Model_Asset($this->request->input('asset_id'));

        $filename = Arr::pluck($_FILES, 'tmp_name');
        $filename = $filename[0][0];

        $asset->replace_with_file($filename);

        $this->response
            ->headers('Content-Type', static::JSON_RESPONSE_MIME)
            ->body(json_encode([$asset->getId()]));
    }
}
