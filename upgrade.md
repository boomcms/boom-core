# BoomCMS Upgrade Notes

## v5.0

* Before updrading to v5.0 v5.1 sites should first be upgraded to v4.3 to ensure all database migrations are run.

## v4.2 - v4.3

* The $asset variable is no longer set in asset chunk views by default. Use as a callback - $asset() - to get the asset instead. 
* Location chunks: address and title sections are now disabled by default and will need to be manually enabled
* The $description() view helper should now be used to insert page meta descriptions rather than calling $page->getDescription() directly
