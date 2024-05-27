<?php
namespace Pephpit\HttpClient\tests\units;

require_once realpath(dirname(__FILE__)).'/../../config/config.php';

use Pephpit\HttpClient\ResponseCollection as TestedClass;
use mageekguy\atoum;

class ResponseCollection extends atoum\test {

	public function testSetGet() {
		$response = new \Pephpit\HttpClient\Response('', array(), '');

		$ResponseCollection = new TestedClass();
		$ResponseCollection->add($response);
		$ResponseCollection->set('response1', $response);
		$ResponseCollection['response2'] = $response;

		$this->object($ResponseCollection[0])->isInstanceOf('\\Pephpit\HttpClient\\Response');
		$this->object($ResponseCollection['response1'])->isInstanceOf('\\Pephpit\HttpClient\\Response');
		$this->object($ResponseCollection->get('response2'))->isInstanceOf('\\Pephpit\HttpClient\\Response');
		$this->variable($ResponseCollection['unknow'])->isNull();
	}

	public function testExceptions() {
		$ResponseCollection = new TestedClass();

		$this->exception(
			function() use($ResponseCollection) {
				$ResponseCollection->add(new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Pephpit\HttpClient\Response');

		$this->exception(
			function() use($ResponseCollection) {
				$ResponseCollection->set('stdclass', new \stdClass());
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Pephpit\HttpClient\Response');

		$this->exception(
			function() use($ResponseCollection) {
			$ResponseCollection['stdclass'] = new \stdClass();
			}
		)->isInstanceOf('\\InvalidArgumentException')->hasMessage('$response must be an instance of \Pephpit\HttpClient\Response');

	}


}
