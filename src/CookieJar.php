<?php
namespace Pephpit\HttpClient;

class CookieJar
{

    private $enable = true;

    private $directory = '/tmp';

    public function enable() {
        $this->enable = true;
    }

    public function disable() {
        $this->enable = false;
    }

    public function getDirectory() {
		return $this->directory;
	}

    public function setDirectory($directory) {
		$this->directory = $directory;
		return $this;
	}

    public function get(string $host) {
		if(empty($host)) {
			throw new \LogicException('host cannot be empty');
        }

        if(is_dir($this->directory) === false) {
			throw new \LogicException($this->directory.' is not a valid directory');
        }
        
        if(is_writable ($this->directory) === false) {
			throw new \LogicException($this->directory.' is not writable');
		}
        
		return $this->directory.'/cookies_'.$host;
	}

    public function reset() {
		if(file_exists($this->get()))
			unlink($this->getCookieJar());
	}

}
