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
