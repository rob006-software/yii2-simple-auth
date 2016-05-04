<?php

/*
 * This file is part of the yii2-simple-auth.
 *
 * Copyright (c) 2016 Robert Korulczyk <robert@korulczyk.pl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace carriera\simpleauth;

/**
 * Action filter for validating simple auth.
 *
 * @author Robert Korulczyk <robert@korulczyk.pl>
 */
class BehaviorFilter extends \yii\base\ActionFilter {

	public $actions = [];

	public $exclude = [];

	/**
	 * @param \yii\base\ActionEvent $event
	 * @return boolean
	 * @throws \yii\web\HttpException when the request method is not allowed.
	 */
	 
	public function beforeAction($event)
	{
		// validation

		return $event->isValid;
	}
}
