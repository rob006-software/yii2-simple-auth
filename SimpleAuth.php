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
 * Action filter for validating simple auth.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class SimpleAuth extends yii\base\ActionFilter {

	/**
	 * @todo
	 * {@inheritdoc}
	 */
	public function beforeAction($action) {
		return parent::beforeAction($action);
	}

}
