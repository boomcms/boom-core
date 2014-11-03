<?php

use \Boom\Chunk\Timestamp as ChunkTimestamp;

class Controller_Cms_Chunk_Timestamp extends Controller_Cms_Chunk
{
    protected $_type = 'timestamp';

    public function action_edit()
    {
        $formats = [];
        foreach (ChunkTimestamp::$formats as $format) {
            $formats[$format] = date($format, $_SERVER['REQUEST_TIME']);
        }

        $this->template = new View('boom/editor/slot/timestamp', [
            'timestamp' => 0,
            'format' => ChunkTimestamp::$default_format,
            'formats' => $formats,
        ]);
    }

    protected function _preview_chunk()
    {
        $chunk = new ChunkTimestamp($this->page, $this->_model, $this->request->post('slotname'));

        return $chunk->execute();
    }

    protected function _preview_default_chunk()
    {
        $chunk = new ChunkTimestamp($this->page, new Model_Chunk_Timestamp(), $this->request->post('slotname'));

        return $chunk->execute();
    }
}
