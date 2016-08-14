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
use yii\web\ForbiddenHttpException;

/**
 * Action filter for validating simple auth token.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class ActionFilter extends \yii\base\ActionFilter {

	/**
	 * List of available authentication methods.
	 * @var array
	 */
	public $allowedMethods = [
		Authenticator::METHOD_HEADER,
		Authenticator::METHOD_GET,
		Authenticator::METHOD_POST,
	];

	/**
	 * Token duration in seconds - if token is older than that, it will be treated as invalid.
	 * For better security this value should be low, but if client or server has wrong time settings,
	 * increasing this value may help. By default it is set to 5 minutes.
	 * @var int
	 */
	public $tokenDuration = 300;

	/**
	 * Name of header used for authentication.
	 * @var string
	 */
	public $headerName = Authenticator::HEADER_NAME;

	/**
	 * Name of POST param used for authentication.
	 * @var string
	 */
	public $postParamName = Authenticator::PARAM_NAME;

	/**
	 * Name of GET param used for authentication.
	 * @var string
	 */
	public $getParamName = Authenticator::PARAM_NAME;

	/**
	 * Secret key used for generate token. Leave empty to use secret from
	 * config (Yii::$app->params['simpleauth']['secret']).
	 * @var string
	 */
	public $secret;

	/**
	 * {@inheritdoc}
	 */
	public function beforeAction($action) {
		if (in_array(Authenticator::METHOD_HEADER, $this->allowedMethods) && $this->validateByHeader()) {
			return parent::beforeAction($action);
		}

		if (in_array(Authenticator::METHOD_GET, $this->allowedMethods) && $this->validateByGetParam()) {
			return parent::beforeAction($action);
		}

		if (in_array(Authenticator::METHOD_POST, $this->allowedMethods) && $this->validateByPostParam()) {
			return parent::beforeAction($action);
		}

		throw new ForbiddenHttpException('No authentication token found.');
	}

	/**
	 * Try to validate request by header token.
	 *
	 * @return bool False if no header with token was found. True if header exist and token is valid.
	 * @throws ForbiddenHttpException When header with token exist, but token is invalid.
	 */
	protected function validateByHeader() {
		if (!Yii::$app->getRequest()->getHeaders()->has($this->headerName)) {
			return false;
		}

		$token = Yii::$app->getRequest()->getHeaders()->get($this->headerName);
		$url = Yii::$app->getRequest()->getAbsoluteUrl();
		return $this->validate($token, $url);
	}

	/**
	 * Try to validate request by GET param with token.
	 *
	 * @return bool False if no GET param with token was found. True if param exist and token is valid.
	 * @throws ForbiddenHttpException When param with token exist, but token is invalid.
	 */
	protected function validateByGetParam() {
		if (!Yii::$app->getRequest()->get($this->getParamName)) {
			return false;
		}

		$url = Yii::$app->getRequest()->getAbsoluteUrl();
		$token = Yii::$app->getRequest()->get($this->getParamName);

		// we need remove token param from URL - token was generated for URL without it
		$query = Yii::$app->getRequest()->getQueryString();
		$token_query = http_build_query([$this->getParamName => $token]);
		if (strpos($query, '&' . $token_query) !== false) {
			$url = str_replace('&' . $token_query, '', $url);
		} else {
			$url = str_replace('?' . $token_query, '', $url);
		}

		return $this->validate($token, $url);
	}

	/**
	 * Try to validate request by POST param with token.
	 *
	 * @return bool False if no POST param with token was found. True if param exist and token is valid.
	 * @throws ForbiddenHttpException When param with token exist, but token is invalid.
	 */
	protected function validateByPostParam() {
		if (!Yii::$app->getRequest()->post($this->postParamName)) {
			return false;
		}

		$token = Yii::$app->getRequest()->post($this->postParamName);
		$url = Yii::$app->getRequest()->getAbsoluteUrl();
		return $this->validate($token, $url);
	}

	/**
	 * Check if token for specified URL is valid.
	 *
	 * @param string $token
	 * @param string $url
	 * @return bool True if token is valid.
	 * @throws ForbiddenHttpException When token is invalid or expired.
	 */
	protected function validate($token, $url) {
		list($hash, $time) = explode('_', $token);

		if (!$this->validateTimestamp($time)) {
			throw new ForbiddenHttpException('Token expired.');
		}
		if (!Token::validate($hash, $url, $time, $this->secret)) {
			throw new ForbiddenHttpException('Invalid token.');
		}

		return true;
	}

	/**
	 * Check if given timestamp has not expired.
	 * 
	 * @param int $timestamp
	 * @return bool True if timestamp has not expired, false otherwise.
	 */
	protected function validateTimestamp($timestamp) {
		return $timestamp >= (time() - $this->tokenDuration);
	}

}
