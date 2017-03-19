<?php
namespace PharIo\FileSystem;

class File {

    /**
     * @var Filename
     */
    private $filename;

    /**
     * @var string
     */
    private $content;

    /**
     * @param Filename $filename
     * @param string   $content
     */
    public function __construct(Filename $filename, $content) {
        $this->filename = $filename;
        $this->content = $content;
    }

    /**
     * @return Filename
     */
    public function getFilename() {
        return $this->filename;
    }

    /**
     * @param Filename $filename
     */
    public function saveAs(Filename $filename) {
        file_put_contents($filename->asString(), $this->getContent());
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
}
