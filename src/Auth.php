<?php
namespace Pephpit\HttpClient;

class Auth
{
	const METHODS = [
		'basic' => CURLAUTH_BASIC,
		'digest' => CURLAUTH_DIGEST,
		'bearer' => CURLAUTH_BEARER,
	];

    private $method = 'basic';
    private $username;
    private $password;
    private $bearer;

    public function getMethod() {
		return $this->method;
	}

	public function setMethod($method) {
		if(array_key_exists($method, self::METHODS) === false) {
			throw new \InvalidArgumentException('Method '.$method.' not available');
		}

		$this->method = $method;
		return $this;
	}

    public function getUsername() {
		return $this->username;
	}

	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	public function getPassword() {
		return $this->password;
	}

	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

    /**
     * Get the value of bearer
     */ 
    public function getBearer() {
        return $this->bearer;
    }

    /**
     * Set the value of bearer
     *
     * @return  self
     */ 
    public function setBearer($bearer) {
        $this->bearer = $bearer;
        return $this;
	}
	
	public function getMethodValue() {
		return self::METHODS[$this->method];
	}
}

