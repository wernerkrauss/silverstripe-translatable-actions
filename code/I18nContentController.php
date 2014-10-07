<?php

class I18nContentController extends Controller implements i18nEntityProvider {

	private static $allowed_actions = array(
		'index',
		'show',
		'tag',
		'author'
	);

	private static $url_handlers = array(
		'show/$ID/$OtherID' => 'show',
		'author/$AuthorID/$Foo' => 'author'
	);


	public function init()
	{
		parent::init();

		//generate $url_handler array and overwrite $config

		$translatedUrlHandlers = array();

		$urlHandlers = $this->stat('url_handlers');

		foreach ($this->allowedActions() as $action) {
			$translatedAction = $this->getTranslatedActionName($action);
			if ($translatedAction != $action) {
				$urlHandler =
				$translatedUrlHandlers[$translatedAction] = $action;
			}
		}

		// update config
		$this->config()->url_handlers = $translatedUrlHandlers;
	}




	/**
	 * @inheritdoc
	 */
	public function provideI18nEntities()
	{
		$entities = array();
		foreach ($this->stat('allowed_actions') as $action) {
			$entities["{$this->class}.action_{$action}"] = array(
				$action,
				'Action ' . $action . ' on controller ' . $this->class
			);
		}
		return $entities;
	}

	/**
	 * @inheritdoc
	 */
	public function Link($action = null)
	{
		//return parent::Link($this->getTranslatedActionName($action)); //works if we have a related page and subclass ContentController
		return Controller::join_links(get_class($this), $this->getTranslatedActionName($action));
	}


	/**
	 * Tranlsates the name of the action.
	 *
	 * @param string $action
	 * @return string
	 */
	public function getTranslatedActionName($action){
		//we have to check parent classes as well
		$class = $this->definingClassForAction($action);
		return _t("{$class}.action_{$action}", $action);
	}


	public function index(){
		return $this->customise(array('Action' => 'index'))->renderWith(array('I18nContentController', 'Page'));

	}

	public function show()
	{
		return $this->customise(array('Action' => 'show'))->renderWith(array('I18nContentController', 'Page'));
	}

	public function tag()
	{
		return $this->customise(array('Action' => 'tag'))->renderWith(array('I18nContentController', 'Page'));	}

	public function author(){
		return $this->customise(array('Action' => 'author'))->renderWith(array('I18nContentController', 'Page'));
	}
}
