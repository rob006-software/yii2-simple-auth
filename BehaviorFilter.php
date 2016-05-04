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
class BehaviorFilter extends \yii\base\Behavior {

	public $actions = [];

	public $exclude = [];

	/**
	 * Declares event handlers for the [[owner]]'s events.
	 * @return array events (array keys) and the corresponding event handler methods (array values).
	 */
	public function events()
	{
		return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
	}

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
