V2.4.1
- Migrated to latest Crypto Library

V2.4
- Migrate to GitHub and rename to scrmhub-wordpress
- Mainly code tidying ready for the GitHub move
- SESSION library now lazy starts if it needs to (e.g. won't force a session_start call)
- Removed uuid from session. Now rely's on COOKIE and Wordress User code
- Added SSL Cookie setting for https sites

V2.3
- Updated Installer and Uninstaller scripts
- Added default configurations
- Encrypted Client secret
- Inline help files

V2.2
- Added Monolog for error catching
- Added Array Helper methods
- Added new settings
*- Added Option to report errors to SCRM Hub

V2.1
- Added Cron jobs
- Implement SCRM Hub URls in sharing

Changes 7th January 2016 V2.0.11
- Update: More forceful new user email suppression

Changes 5th January 2016 V2.0.10
- Update: Use Permalink for the share
- Update: Popups now self close (again)

Changes 5th January 2016 V2.0.9
- Update: Tweaked the tracking code for the redirect to be faster
- Fixed:  Changed share url system to be better namespace'd

Changes 11th December V2.0.7
- Update: Changed binding methods on connect and share to be much neater and efficent
- Update: Added 'bind' method to share and connect. e.g bind(selector) which will find all buttons in the target
- Update: Added global 'bind' method that will trigger the connect and share bindings. Usage: window.scrmhub.bing(selector);

Changes 10th December V2.0.6
- Additional cache busting headers
- Stronger class typing

Changes 9th December V2.0.5
- Added: New check to analytics to make absolutely sure 
- Fixed: Bug that prevented the connect dialogue opening in a popup on desktops
- Fixed: Analytics was missing the referrer value

Changes 9th December V2.0.4
- Added: Forceful error message if Facebook Share is not properly configured
- Updated: Load order of Wordpress hooks
- Updated: JS Libraries now load faster with less page overhead
- Updated: Popup close now dom driven from JS
- Fixed: Lots of little bugs in the Auto Update code (still in private Beta though as the REPO is not public yet)
- Fixed: JS Mobile detect is now using platform detection, not a media query (was breaking IE9)
- Removed: Server side mobile detect classes as not used
- Removed: Several warnings if WP_Debug is turned on

Changes 7th December V2.0.3
- Rolled back sharing to server side
- Added new utils class and functions to JS
- Tweaked tracking values server and client side
- Fixed a bug where an activity track could fail if called too early

Changes 4th Decemeber V2.0.2
- Update: Share is now done in JS
- Update: Connect is now it's own JS Library
- Update: Now throws a big error if you try to render a share or connect button that isn't enabled in settings
- Update: SCRM Hub JS libraries now have private init() method and public ready() function
- Fixed: bug where share button would render even if the network was disabled
- Fixed: bug where local storage and cookie value didn't match

Changes 3rd December V2.0.1
- Added: Share click through activity track
- Added: Ajax endpoint to get user uuid and check if connected
- Added: Client side flag to stop repeatedly checking the user is connected
- Added: Ajax version of login to support cached sites
- Update: Migrated to live APIs not demo APIs
- Update: Changed Person and Cookie class to be core not Website to correctly support AJAX calls
- Update: Latest SCRM Hub API code
- Update: Sharing is now it's own JS Library
- Update: Logout now returns the user to the page they were viewing
- Update: Exposed API and AJAX urls to front-end
- Update: Finally moved some Wordpress hooks that had been giving me the hibbie-jibbies
- Update: Auto update added, but currently disabled
- Fixed: Incorrect token name in JS compared to server side

Changes 30th November
- Added Activity tracking optons
- Small optimisation in Networks class
- Added Auto-Update option to App Settings page

Changes 26th November 2015
- Disabled short url system for now whilst we're upgrading
- Added 'icon only' option to connect and share buttons
- Improved CSS construction on buttons
- Simplified options
- Converted true/false values in admin to more reliable version (will require re-saving all settings pages with yes/no boxes)
- Added a confirmation message to settings that will update the whole network
- Re-worked error message handling
- Added parent_function option to connecting. Add this as an argument to the connect render, and when connect finishes, this will be called in the parent window