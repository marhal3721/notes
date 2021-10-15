<?php

namespace Image\View;

class SaveView
{
    private $fileRelativePath;

    public function __construct(string $fileRelativePath)
    {
        $this->fileRelativePath = $fileRelativePath;
    }

    public function __destruct()
    {
        unset($this->fileRelativePath);
    }

    protected function getFileRelativePath() : string
    {
        return $this->fileRelativePath;
    }

    public function render()
    {
        return $this->getFileRelativePath();
    }
}
