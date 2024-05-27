<?php
namespace Pephpit\HttpClient;

use InvalidArgumentException;
use LogicException;

/**
 * Send Rest request
 */
class Request {

    // @property ressource $ch Curl Handler
    protected $ch;
    // @property ressource $fh File Handler
    protected $fh;

    protected $timeout = 5;

    protected $url;
    protected $method;
    protected $body;
    protected $requestLength = 0;

    protected $headers = ['Accept' => MineType::JSON];
    protected $userAgent;

    protected $httpVersion = HttpVersion::DEFAULT;
    protected $sslVersion = SslVersion::DEFAULT;
    protected $sslVerify = true;

    protected $followLocation = true;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var CookieJar
     */
    protected $cookieJar;

    /**
     * Contruct HttpClient
     * @param string $url url to call
     * @param string $method http method
     * @param string|array $body array of parameter or string to send
     */
    public function __construct(string $url = null, string $method = Method::GET, $body = null) {
        $this->url = $url;
        $this->method = $method;
        $this->body = $body;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function setUrl(string $url) {
        $this->url = $url;
        return $this;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function setMethod(string $method) {
        $this->method = $method;
        return $this;
    }

    public function getTimeout(): int {
        return $this->timeout;
    }

    public function setTimeout(int $timeout) {
        $this->timeout = $timeout;
        return $this;
    }

    public function getHeaders(): array {
        return $this->headers;
    }

    public function getHeader(string $name): string {
        return $this->headers[$name];
    }

    public function setHeader(string $name, string $value) {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getContentType(): string {
        return $this->headers['Content-Type'];
    }

    public function setContentType(string $contentType) {
        $this->headers['Content-Type'] = $contentType;
        return $this;
    }

    public function getAcceptType(): string {
        return $this->headers['Accept'];
    }

    public function setAcceptType(string $acceptType) {
        $this->headers['Accept'] = $acceptType;
        return $this;
    }

    public function getSslVerify(): bool {
        return $this->sslVerify;
    }

    public function setSslVerify(bool $sslVerify) {
        $this->sslVerify = $sslVerify;
        return $this;
    }

    public function getSslVersion(): bool {
        return $this->sslVersion;
    }

    public function setSslVersion(int $sslVersion) {
        $this->sslVersion = $sslVersion;
        return $this;
    }

    public function getFollowLocation(): bool {
        return $this->followLocation;
    }

    public function setFollowLocation(bool $followLocation) {
        $this->followLocation = $followLocation;
        return $this;
    }

    public function setbody(string $body) {
        $this->body = $body;
        return $this;
    }

    public function setRequestLength($requestLength) {
        $this->requestLength = $requestLength;
        return $this;
    }

    public function getUserAgent() {
        return $this->userAgent;
    }

    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Init Curl handler and options
     * @throws InvalidArgumentException
     */
    public function init() {
        $this->ch = curl_init();

        switch (strtoupper($this->method)) {
            case Method::GET:
                $this->buildBody();
                $this->initGet();
                break;
            case Method::POST:
                $this->buildBody();
                $this->initPost();
                break;
            case Method::PUT:
                $this->buildBody();
                $this->initPut();
                break;
            case Method::DELETE:
                $this->initDelete();
                break;
            default:
                throw new InvalidArgumentException('Current verb (' . $this->method . ') is an invalid REST method.');
        }

        $this->initCurlOpts();
        $this->initHeaders();
        $this->initAuth();
    }

    /**
     * Transform array of parameters to string
     * @param array $data
     */
    protected function buildBody() {
        if ( is_array($this->body) ) {
            // if there's a file, don't transform to string
            foreach($this->body as $element) {
                if($element instanceof \CURLFile)
                    return;
            }

            $this->body = http_build_query($this->body, '', '&');
        }
    }

    /**
     * Execute the request
     * Return Pephpit\HttpClient\Response
     */
    public function send(): Response {

        $this->init();
        return new Response(
            curl_exec($this->ch),
            curl_getinfo($this->ch),
            curl_error($this->ch),
            ResponseHeaders::get($this->ch)
        );

    }

    /**
     * Init Get Request
     */
    protected function initGet() {
        if(!empty($this->body))
            $this->url .= ( strpos($this->url, '?') === false ) ? '?'.$this->body : '&'.$this->body;
    }

    /**
     * Init Post Request
     */
    protected function initPost() {
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->body);
    }

    /**
     * Init Put Request
     */
    protected function initPut() {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        if( !empty($this->body) ){
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->body);
        }
    }

    /**
     * Init Delete Request
     */
    protected function initDelete() {
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if( !empty($this->body) ){
            curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->body);
        }

    }

    /**
     * Define common curl options
     */
    protected function initCurlOpts() {

        if(empty($this->url)){
            throw new LogicException('Url must be set');
        }

        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerify);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $this->sslVerify);
        curl_setopt($this->ch, CURLOPT_SSLVERSION, $this->sslVersion);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, $this->followLocation);
        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, '\Pephpit\HttpClient\ResponseHeaders::callback');

        if(!empty($this->userAgent)) {
            curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);
        }

        if($this->cookieJar instanceof CookieJar) {
            curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookieJar->get());
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookieJar->get());
        }
    }

    protected function initHeaders() {

        $headers = array();

        foreach ($this->headers as $name => $value) {
            $headers[] = $name.': '.$value;
        }

        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers );
    }

    /**
     * Set Basic auth curl options
     */
    protected function initAuth() {
        if ($this->auth instanceof Auth) {
            curl_setopt($this->ch, CURLOPT_HTTPAUTH, $this->auth->getMethodValue());

            if ($this->auth->getMethod() == 'basic' || $this->auth->getMethod() == 'digest') {
                curl_setopt($this->ch, CURLOPT_USERPWD, $this->auth->getUsername() . ':' . $this->auth->getPassword());
            }
            else if($this->auth->getMethod() == 'bearer') {
                curl_setopt($this->ch, CURLOPT_XOAUTH2_BEARER, $this->auth->getBearer());
            }
        }
    }

    /**
     * Check if curl handler is init
     * @throws LogicException
     */
    protected function checkCurlHandler() {
        if( is_resource($this->ch) != 'curl' ){
            throw new LogicException('Curl handler not initialized');
        }
    }

    /**
     * Return curl ressource, work only from MultiClient
     * @return ressource Curl
     * @throws LogicException
     */
    public function getCurlHandler(){
        $this->checkCurlHandler();
        return $this->ch;
    }

    /**
     * Close curl handler and file handler
     */
    public function __destruct() {

        if( !empty($this->fh) ){
            fclose($this->fh);
        }

        if( !empty($this->ch) ){
            curl_close($this->ch);
        }
    }
}

