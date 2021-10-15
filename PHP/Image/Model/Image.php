<?php
namespace Image\Model;

use Grafika\EditorInterface;
use Grafika\Grafika;

abstract class Image implements IImage
{
    //这里是由Grafika::createEditor()->open($image, $path)
    private $image;
    private $editor;
    private $process;
    protected $width;//缩放图的宽度
    protected $height;//缩放图的高度

    public function __construct(array $process, $editor, $image)
    {
        $this->width = 0;
        $this->height = 0;
        $this->process = $process;
        $this->image = $image;
        $this->editor = $editor;
    }

    public function __destruct()
    {
        unset($this->image);
        unset($this->editor);
        unset($this->process);
        unset($this->width);
        unset($this->height);
    }

    public function setWidth(int $width) : void
    {
        $this->width = $width;
    }
    public function getWidth() : int
    {
        return $this->width;
    }

    public function setHeight(int $height) : void
    {
        $this->height = $height;
    }

    public function getHeight() : int
    {
        return $this->height;
    }

    public function setProcess(array $process)
    {
        $this->process = $process;
    }

    public function getProcess() : array
    {
        return $this->process;
    }
    
    public function setEditor(EditorInterface $editor)
    {
        $this->editor = $editor;
    }
    public function getEditor() : EditorInterface
    {
        return $this->editor;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }
    public function getImage()
    {
        return $this->image;
    }

    public function getBlankEditor()
    {
        return Grafika::createEditor();
    }

    public function getBlankImage($width = 1, $height = 1)
    {
        return Grafika::createBlankImage($width, $height);
    }
}
