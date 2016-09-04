<?php

/*
 * This file is part of the yii2-simple-auth.
 *
 * Copyright (c) 2016 Robert Korulczyk <robert@korulczyk.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace rob006\simpleauth;

/**
 * Helper class for authentication requests.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 * @since 1.0.0
 */
abstract class Authenticator {

	const METHOD_HEADER = 'header';
	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	const HEADER_NAME = 'X-Simple-Auth-Token';
	const PARAM_NAME = 'simple_auth_token';

	/**
	 * Request for authenticate.
	 * @var mixed
	 */
	protected $request;

	/**
	 * Secret key used for authenticate.
	 * @var string
	 */
	protected $secret;

	/**
	 * Authenticate given Request object by specified method.
	 *
	 * @param mixed $request Request object.
	 * @param string $method Authentication method.
	 * @param string $secret Secret key used for generate token. Leave empty to use secret from
	 * config (Yii::$app->params['simpleauth']['secret']).
	 * @return mixed Authenticated Request object.
	 * @throws \yii\base\InvalidParamException
	 */
	public static function authenticate($request, $method = self::METHOD_HEADER, $secret = null) {
		$authenticator = new static();
		$authenticator->request = $request;
		$authenticator->secret = $secret;
		$authenticator->validateRequest();

		switch ($method) {
			case static::METHOD_HEADER:
				return $authenticator->authenticateByHeader();
			case static::METHOD_GET:
				return $authenticator->authenticateByGetParam();
			case static::METHOD_POST:
				return $authenticator->authenticateByPostParam();
			default:
				throw new \yii\base\InvalidParamException('Incorrect authentication method.');
		}
	}

	/**
	 * Generate authentication token.
	 *
	 * @param string $url URL for authenticate.
	 * @param string $secret Secret key used for generate token. Leave empty to use secret from
	 * config (Yii::$app->params['simpleauth']['secret']).
	 * @return string
	 */
	public static function generateAuthToken($url, $secret = null) {
		$time = time();
		return Token::generate($url, $time, $secret) . '_' . $time;
	}

	/**
	 * Check if given Request object has correct type.
	 *
	 * @throws \yii\base\InvalidParamException When $request has invalid type.
	 */
	abstract protected function validateRequest();

	/**
	 * Add authentication token to header of authenticated Request.
	 *
	 * @return mixed Authenticated Request object.
	 */
	abstract protected function authenticateByHeader();

	/**
	 * Add authentication token to GET param of authenticated Request.
	 *
	 * @return mixed Authenticated Request object.
	 */
	abstract protected function authenticateByGetParam();

	/**
	 * Add authentication token to POST param of authenticated Request.
	 *
	 * @return mixed Authenticated Request object.
	 */
	abstract protected function authenticateByPostParam();
}
