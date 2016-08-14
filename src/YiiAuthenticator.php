<?php

/*
 * This file is part of the yii2-simple-auth.
 * 
 * Copyright (c) 2016 Robert Korulczyk <robert@korulczyk.pl>.
 * 
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace rob006\simpleauth;

/**
 * Helper class for authenticate \yii\httpclient\Request from yiisoft/yii2-httpclient.
 * @see https://github.com/yiisoft/yii2-httpclient
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class YiiAuthenticator extends Authenticator {

	/**
	 * {@inheritdoc}
	 * Handle \yii\httpclient\Request from yiisoft/yii2-httpclient.
	 * @see https://github.com/yiisoft/yii2-httpclient
	 *
	 * @param \yii\httpclient\Request $request Request object.
	 * @param string $method
	 * @param string $secret Secret key used for generate token. Leave empty to use secret from
	 * config (Yii::$app->params['simpleauth']['secret']).
	 * @return \yii\httpclient\Request Authenticated Request object.
	 * @throws \yii\base\InvalidParamException
	 */
	public static function authenticate($request, $method = self::METHOD_HEADER, $secret = null) {
		return parent::authenticate($request, $method, $secret);
	}

	/**
	 * {@inheritdoc}
	 * Require \yii\httpclient\Request from yiisoft/yii2-httpclient.
	 * @see https://github.com/yiisoft/yii2-httpclient
	 */
	protected function validateRequest() {
		if (!class_exists('\yii\httpclient\Request')) {
			throw new \yii\base\Exception('Class \yii\httpclient\Request does not exist. '
			. 'Package yiisoft/yii2-httpclient should be installed to use this Authenticator.');
		}
		if (!($this->request instanceof \yii\httpclient\Request)) {
			throw new \yii\base\InvalidParamException('$request should be instance of \yii\httpclient\Request');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function authenticateByHeader() {
		$copy = clone $this->request;
		return $this->request->addHeaders([
					static::HEADER_NAME => static::generateAuthToken($copy->prepare()->getUrl(), $this->secret),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function authenticateByGetParam() {
		$copy = clone $this->request;
		$this->request->setMethod('get');
		return $this->request->setData(array_merge((array) $this->request->data, [
					static::PARAM_NAME => static::generateAuthToken($copy->prepare()->getUrl(), $this->secret),
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function authenticateByPostParam() {
		$this->request->setMethod('post');
		$copy = clone $this->request;
		return $this->request->setData(array_merge((array) $this->request->data, [
					static::PARAM_NAME => static::generateAuthToken($copy->prepare()->getUrl(), $this->secret),
		]));
	}

}
