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

use Yii;
use yii\base\InvalidParamException;

/**
 * Helper class for generate auth tokens.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 * @since 1.0.0
 */
class Token {

	/**
	 * Name of hashing algorithm used for generate token.
	 *
	 * @var string
	 * @see https://secure.php.net/manual/en/function.hash.php
	 */
	public static $algorithm = 'sha1';

	/**
	 * Generate token for specified parameteres.
	 *
	 * @param string $url URL for generate token.
	 * @param int|null $time Timestamp for token. Leave empty to use current timestamp.
	 * @param string|null $secret Secret key used for generate token. Leave empty to use
	 * Yii::$app->params['simpleauth']['secret'] value by default.
	 * @return string
	 */
	public static function generate($url, $time = null, $secret = null) {
		$secret = static::getSecret($secret);

		if (empty($time)) {
			$time = time();
		}

		return static::generateToken($url, $secret, $time);
	}

	/**
	 * Check if token is valid for specified parameters.
	 *
	 * @param string $token Token for validation.
	 * @param string $url
	 * @param int $time
	 * @param string|null $secret Secret key used for generate token. Leave empty to use
	 * Yii::$app->params['simpleauth']['secret'] value by default.
	 * @return bool
	 */
	public static function validate($token, $url, $time, $secret = null) {
		$secret = static::getSecret($secret);

		return $token === static::generate($url, $time, $secret);
	}

	/**
	 * Validate secret key. If $secret is empty, Yii::$app->params['simpleauth']['secret'] is used.
	 *
	 * @param string|null $secret
	 * @return string
	 * @throws InvalidParamException
	 */
	protected static function getSecret($secret = null) {
		if (empty($secret)) {
			if (empty(Yii::$app->params['simpleauth']['secret'])) {
				throw new InvalidParamException('Invalid or missing secret phrase.');
			} else {
				$secret = Yii::$app->params['simpleauth']['secret'];
			}
		}

		if (!is_string($secret)) {
			throw new InvalidParamException('Secret phrase must be a string.');
		}

		return $secret;
	}

	/**
	 * Generate token for specified parameters.
	 *
	 * @param string $url
	 * @param string $secret
	 * @param int $time
	 * @return string
	 */
	protected static function generateToken($url, $secret, $time) {
		return hash(static::$algorithm, implode(' ', [$url, $secret, $time]));
	}

}
