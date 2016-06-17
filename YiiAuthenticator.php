<?php

/*
 * This file is part of the yii2-simple-auth.
 * 
 * Copyright (c) 2016 Robert Korulczyk <robert@korulczyk.pl>.
 * 
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace salenauts\simpleauth;

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
	 * @return \yii\httpclient\Request Authenticated Request object.
	 * @throws \yii\base\InvalidParamException
	 */
	public static function authenticate($request, $method = self::METHOD_HEADER) {
		return parent::authenticate($request, $method);
	}

	/**
	 * {@inheritdoc}
	 * Require \yii\httpclient\Request from yiisoft/yii2-httpclient.
	 * @see https://github.com/yiisoft/yii2-httpclient
	 */
	protected static function validateRequest($request) {
		if (!class_exists('\yii\httpclient\Request')) {
			throw new \yii\base\Exception('Class \yii\httpclient\Request does not exist. '
			. 'Package yiisoft/yii2-httpclient should be installed to use this Authenticator.');
		}
		if (!($request instanceof \yii\httpclient\Request)) {
			throw new \yii\base\InvalidParamException('$request should be instance of \yii\httpclient\Request');
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param \yii\httpclient\Request $request
	 * @return \yii\httpclient\Request
	 */
	protected static function authenticateByHeader($request) {
		$copy = clone $request;
		return $request->addHeaders([
					static::HEADER_NAME => static::generateAuthToken($copy->prepare()->getUrl()),
		]);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param \yii\httpclient\Request $request
	 * @return \yii\httpclient\Request
	 */
	protected static function authenticateByGetParam($request) {
		$copy = clone $request;
		$request->setMethod('get');
		return $request->setData(array_merge((array) $request->data, [
					static::PARAM_NAME => static::generateAuthToken($copy->prepare()->getUrl())
		]));
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param \yii\httpclient\Request $request
	 * @return \yii\httpclient\Request
	 */
	protected static function authenticateByPostParam($request) {
		$request->setMethod('post');
		$copy = clone $request;
		return $request->setData(array_merge((array) $request->data, [
					static::PARAM_NAME => static::generateAuthToken($copy->prepare()->getUrl())
		]));
	}

}
