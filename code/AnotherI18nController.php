<?php

/**
 * Class AnotherI18NController
 *
 * Testing url handlers from parent....
 */
class AnotherI18nController extends I18nContentController
{
	private static $allowed_actions = array(
		'someOtherAction'
	);

	public function init(){
		parent::init();
	}


	public function someOtherAction(){
		return $this->customise(array('Action' => 'someOtherAction'))->renderWith(array('I18nContentController', 'Page'));
	}

}