<?php
namespace Pephpit\HttpClient;

class File {

    private $filename;
    private $mime;
    private $name;

    public function __construct(string $filename, string $mine, $name = null) {
        $this->filename = $filename;
        $this->mime = $mime;
        $this->name = $name;
    }

    /**
     * Get the value of filename
     */ 
    public function getFilename() {
        return $this->filename;
    }

    /**
     * Set the value of filename
     *
     * @return  self
     */ 
    public function setFilename($filename) {
        $this->filename = $filename;

        return $this;
    }  

    /**
     * Get the value of mime
     */ 
    public function getMime() {
        return $this->mime;
    }

    /**
     * Set the value of mime
     *
     * @return  self
     */ 
    public function setMime($mime) {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName() {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name) {
        $this->name = $name;

        return $this;
    }
}
