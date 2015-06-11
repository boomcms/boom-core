<?php

namespace Boom\Asset\Processor;

class MSExcel extends Processor
{
    public function thumbnail($width = null, $height = null)
    {
        return $this->response
            ->headers('Content-type', 'image/png')
            ->body(readfile(__DIR__.'/../../../../media/boom/img/ms_excel.png'));
    }
}
