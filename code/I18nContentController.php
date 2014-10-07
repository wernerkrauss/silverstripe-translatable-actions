<?php

/**
 * Class I18nContentController
 */
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
		$this->translateUrlHandlers();
	}

	/**
	 * Updates the config with translated $url_handlers
	 * Uses urlhandler_ and action_ keys for translating existing handlers.
	 * If there is no url_handler for an action it generates a translated default url_handler with /$ID/$OtherID params
	 */
	protected function translateUrlHandlers()
	{
		$translatedUrlHandlers = array();

		foreach ($this->allowedActions() as $action) {
			$translatedAction = $this->getTranslatedActionName($action);
			//do we have already an $url_handler for this action?
			if ($urlHandlers = $this->getUrlHandlersForAction($action)) {
				foreach ($urlHandlers as $urlHandler => $action) {
					$translatedUrlHandler = $this->getTranslatedUrlHandler($urlHandler, $action);
					if ($translatedUrlHandler != $urlHandler) {
						$translatedUrlHandlers[$translatedUrlHandler] = $action;
					}

					//fallback if only the action is translated but not the corresponding url_handler
					$fallback = str_replace($action, $translatedAction, $urlHandler);
					if ($fallback != $urlHandler) {
						$translatedUrlHandlers[$fallback] = $action;
					}
				}
			} elseif ($translatedAction !== $action) {
				//if not use default $ID/$OtherID
				$translatedUrlHandlers[$translatedAction . '/$ID/$OtherID'] = $action;
			}
		}

		// update config
		$this->config()->url_handlers = $translatedUrlHandlers;
	}


	/**
	 * returns all url_handlers that are defined for that action
	 * @param $action
	 * @return array
	 */
	public function getUrlHandlersForAction($action){
		//$class = $this->definingClassForAction($action);
		//$urlHandlers = Config::inst()->get($class, 'url_handlers') ?: array();

		/** @var array $urlHandlers */
		$urlHandlers = $this->config()->url_handlers; //has all url_handlers incl. parent controllers

		return array_filter($urlHandlers, function($url_handler) use($action){return strtolower($url_handler) == $action;});
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
				'Action "' . $action . '" on controller ' . $this->class
			);
		}

		foreach ($this->stat('url_handlers') as $url_handler => $action) {
			$handlerString = $this->sanitiseUrlHandlerName($url_handler);
			$entities["{$this->class}.urlhandler_{$handlerString}"] = array(
				$url_handler,
				'$url_handler "' . $action . '" on controller ' . $this->class
			);
		}

		return $entities;
	}
	/**
	 * Strip an url_holder key of special characters so it is suitable for use as a translation key
	 *
	 * @param string $name
	 * @return string the name with all special characters replaced with underscores
	 */
	protected function sanitiseUrlHandlerName($name) {

		return $this->sanitiseCachename($name); //is there a problem with $ in translation keys?
//		return str_replace(array('~', '.', '/', '!', ' ', "\n", "\r", "\t", '\\', ':', '"', '\'', ';','$'), '_', $name);
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

	/**
	 * @param $urlHandler
	 * @param $action
	 * @return string
	 */
	public function getTranslatedUrlHandler($urlHandler, $action){
		$key = $this->definingClassForAction($action) . '.urlhandler_' . $this->sanitiseUrlHandlerName($urlHandler);
		return _t($key, $urlHandler);
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
