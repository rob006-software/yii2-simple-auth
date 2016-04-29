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
class Authenticator {

	const METHOD_HEADER = 'header';
	const METHOD_GET = 'get';
	const METHOD_POST = 'post';
	const HEADER_NAME = 'X-Simple-Auth-Token';
	const PARAM_NAME = 'simple_auth_token';

	public static function authenticate($request, $method = self::METHOD_HEADER) {

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

	protected static function validateRequest($request) {

	}

	protected static function authenticateByHeader($request) {

	}

	protected static function authenticateByGetParam($request) {

	}

	protected static function authenticateByPostParam($request) {
		
	}

}
