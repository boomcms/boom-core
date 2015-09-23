### [3.2] - 2015-09-23

  * Added ability to link to assets via link picker
  * Added HTML Chunks
  * CSS refactoring - results in a much smaller cms.css file
  * Refactored JavaScript in asset manager with the create of an assetSelection object
  * RESTful URLs for assets
  * Improved localisation
  * Dialogs are now centered with CSS rather than JavaScript to which fixes issues with the dimensions of tabbed dialogs
  * Interface improvements
  * Added a site settings editor
  * Updated timestamp chunks to accept a html string in the same way as text chunks
  * Made the $getTags() helper variadic
  * Removed template button from CMS toolbar
  * Fixes and improvements to the appearance of the page URL list
  * Added the ability for timestamp chunk formats to be hard coded in the template
  * Bugfix: Page title length counter was confused by whitespace at begin or end
  * Bugfix: Fixed video streaming for video assets
  * Bugfix: Refactored page search filter to fix pages appearing multiple times in search results

### [3.1.6] - 2015-09-11

  * Fixed bug in RelatedByTags page filter

### [3.1.5] - 2015-09-09

  * Fixed text editor accept button not working when editor is in inline mode

### [3.1.4] - 2015-09-07

  * Fixed error in child page settings when advanced settings aren't visible

### [3.1.3] - 2015-09-04

  * Bugfix: Chunk\Slideshow\Slide->hasLink() returning true when there's no link

### [3.1.2] - 2015-08-25

  * Updated Tag filter for page finder to accept an array of tags

### [3.1] - 2015-08-17

  * Code refactoring / improvements
  * Minor interface fixes / improvements
  * Improved CMS menu interface with smaller text and FontAwesome icons on menu items
  * Added ability to define page relationships
  * Added a page setting to prevent pages from being deleted
  * Changed dialog colours to light grey background with red header
  * New page settings interface
  * Added isDraft() and isPublished() methods to page version object
  * Improved directory structure to be more consistent with Laravel
  * Fire events on certain page actions
  * Bugfix: After logout editor returns previous state, not disabled
  * Bugfix: Confirmations were missing dialog overlay
  * Bugfix: Error messages about non-existent directories when running the installTemplates command
  * Asset manager: Added ability to replace and revert assets
  * Asset manager: Added basic image editing allowing images to be rotated and cropped
  * Asset manager: Fixed issues with asset uploader when used multiple times without reloading the page
  * Asset manager: Added quicktime video support
  * Page manager: Replaced URLs button with button to access all page settings
  * Page manager: Icons only for add, delete, and settings buttons 
