#DEMO OF TRANSLATABLE ACTIONS#

##Install##
  * clone this repo in your silverstripe root and run ?flush=1
  * translate the lang file to your language
  * set the language to your language in `config.yml`
  * flush
  * call `/I18nContentController/` on your server
  * view at those wonderfully translated actions when you click a link to an action at the bottom of the page.
  
##What it does##
  * it adds the actions to the lang file, prefixed with `action_`. So action  `show` becomes the language key `action_show` which you can translate to your language
  * those language keys are exported when you call the `i18nTextCollectorTask`
  * on init() it generates some extra `$url_handler` and adds them to config API
    * other params are currently ignored, it's just a working demo for now
  * it creates a translated link 
  
##What it does not##
  * it doesn't rewrite links yet
  * it ignores other routing and other params in `$url_handlers`
  * it doesn't bring you a coffee