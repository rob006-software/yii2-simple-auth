<?php

/*
 * This file is part of the yii2-simple-auth.
 *
 * Copyright (c) 2016 Robert Korulczyk <robert@korulczyk.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace salenauts\simpleauth;

/**
 * Helper class for authentication requests.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
abstract class Authenticator {

	const METHOD_HEADER = 'header';
	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	const HEADER_NAME = 'X-Simple-Auth-Token';
	const PARAM_NAME = 'simple_auth_token';

	/**
	 * Authenticate given Request object by specified method.
	 *
	 * @param mixed $request Request object.
	 * @param string $method
	 * @return mixed Authenticated Request object.
	 * @throws \yii\base\InvalidParamException
	 */
	public static function authenticate($request, $method = self::METHOD_HEADER) {
		static::validateRequest($request);

		switch ($method) {
			case static::METHOD_HEADER:
				return static::authenticateByHeader($request);
			case static::METHOD_GET:
				return static::authenticateByGetParam($request);
			case static::METHOD_POST:
				return static::authenticateByPostParam($request);
			default:
				throw new \yii\base\InvalidParamException('Incorrect authentication method.');
		}
	}

	/**
	 * Generate authentication token.
	 *
	 * @param string $url
	 * @return string
	 */
	public static function generateAuthToken($url) {
		$time = time();
		return Token::generate($url, $time) . '_' . $time;
	}

	/**
	 * Check if given Request object has correct type.
	 * 
	 * @param mixed $request Request object to test.
	 * @throws \yii\base\Exception When required class does not exist.
	 * @throws \yii\base\InvalidParamException When $request has invalid type.
	 */
	abstract protected static function validateRequest($request);

	/**
	 * Add authentication token to header of given Request.
	 *
	 * @param mixed $request Request object.
	 * @return mixed Authenticated Request object.
	 */
	abstract protected static function authenticateByHeader($request);

	/**
	 * Add authentication token to GET param of given Request.
	 *
	 * @param mixed $request Request object.
	 * @return mixed Authenticated Request object.
	 */
	abstract protected static function authenticateByGetParam($request);

	/**
	 * Add authentication token to POST param of given Request.
	 *
	 * @param mixed $request Request object.
	 * @return mixed Authenticated Request object.
	 */
	abstract protected static function authenticateByPostParam($request);
}
