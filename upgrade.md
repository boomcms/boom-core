# BoomCMS Upgrade Notes

## v5.3.0

* Themes which use the CreatePage job to create pages should no longer need to fire the PageWasCreated event manually

## v5.2.0

* URL::is() has been renamed URL::matches()
* Following 5.1.7, themes definiing routes should ensure that the 'web' middleware group is included. Session, cookie, and CSRF middleware are no longer set globally

## v5.1.7

* Themes defining routes will now need to ensure that the VerifyCsrfToken is defined as it's now longer set globally
 
## v5.0

* Before updrading to v5.0 v5.1 sites should first be upgraded to v4.3 to ensure all database migrations are run.

## v4.2 - v4.3

* The $asset variable is no longer set in asset chunk views by default. Use as a callback - $asset() - to get the asset instead. 
* Location chunks: address and title sections are now disabled by default and will need to be manually enabled
* The $description() view helper should now be used to insert page meta descriptions rather than calling $page->getDescription() directly
