<?php


class TiramizooApiExposed extends TiramizooApi 
{
    public function __call($method, array $args = array()) {
        if (!method_exists($this, $method))
            throw new BadMethodCallException("method '$method' does not exist");
        return call_user_func_array(array($this, $method), $args);
    }

    public function __construct($api_url, $api_token)
    {
    	parent::__construct($api_url, $api_token);
    }
}

class Unit_Core_TiramizooApi_TiramizooApiTest extends OxidTestCase
{	
	public function setUp()
	{
        parent::setUp();
		$this->_oSubj = new TiramizooApiExposed('http://faketiramizooapi.dev', 'fakeToken');
	}

/*

	Implement fake api at faketiramizooapi.dev using Slim

	index.php

	// <?php
	// require 'vendor/autoload.php';

	// $app = new \Slim\Slim();

	// $app->response()->header('Content-Type', 'application/json');

	// $app->get('/testRequestGet', function () use ($app) {  
	// });

	// $app->post('/testRequestPost', function () use ($app) {  
	// });

	// $app->get('/testStatus500', function () use ($app) {  
	//   $app->response()->status(500);
	// });

	// $app->post('/testTimeOut', function () use ($app) {  
	//   $app->response()->status(200);
	//   sleep(600);
	// });

	// $app->post('/testPostRequestData', function () use ($app) {  
	//     $request = $app->request();
	//     $body = $request->getBody();
	//     $input = json_decode($body);
	//     echo json_encode($input);
	// });

	// // run
	// $app->run();

	composer.json

	{
    	"require": {
        	"slim/slim": "2.*"
    	}
	}

*/

/*
	public function testRequestGet()
	{
		$this->_oSubj->requestGet('testRequestGet', array(), $result);
		$this->assertEquals('200', $result['http_status']);
	}

	public function testRequestPost()
	{
		$this->_oSubj->request('testRequestPost', array(), $result);
		$this->assertEquals('200', $result['http_status']);
	}

	public function testStatus500()
	{
		$this->_oSubj->requestGet('testStatus500', array(), $result);
		$this->assertEquals('500', $result['http_status']);
	}

	public function testStatus404()
	{
		$this->_oSubj->requestGet('testStatus404NotDefinedRoute', array(), $result);
		$this->assertEquals('404', $result['http_status']);
	}

	public function testTimeOut()
	{
		TiramizooApiExposed::setConnectionTimeout(1, 5);
		$this->_oSubj->request('testTimeOut', array(), $result);
		$this->assertEquals(TiramizooApiExposed::CURLE_OPERATION_TIMEDOUT, $result['errno']);
	}

	public function testPostRequestData()
	{
		$oObject = new stdClass();
		$oObject->name = 'tiramizoo';
		$oObject->engine = 'api';

		$this->_oSubj->request('testPostRequestData', $oObject, $result);

		$this->assertEquals($oObject, $result['response']);
	}
*/

	public function testJsonUnescape()
	{
		$aMatchedElements = array(1 => 'some special chars: \u00E4, \u00E8, \u00F6, \u00FC');

		$this->assertEquals('some special chars: ä, è, ö, ü', $this->_oSubj->json_unescape($aMatchedElements));
	}
}
