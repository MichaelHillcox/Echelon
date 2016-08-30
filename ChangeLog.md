# Change Log
---
### Version 2.1.0.1

Tues, `30th August 2016`
- Adding bootstrap pagination to footer
- Updated `clients.php` to use bootstrap for the search section of the page.
- Deprecated `getEchVer()` in preference to `hasUpdate()`
    - Awaiting move to new system.
- Fixed up some issue with `functions.php`

Mon, `29th August 2016`
- Added update channels
    - Alpha, Beta, Stable
    - I've also changed the update url to my personal site.
- Merged `cd.css`, `home.css`, `settings.css` into `master.scss`

Sun, `28th August 2016`
- Refactored `Members.php` class file to `LegacyMembers.php`
    - Prep work for rewrite.
- Fixed spelling mistake in the password reset email.
- Fixed spelling mistake in `register.php`
- Added support to remove footer and container in case of an odd page like the login screen
- Removed `login.css`
- Added new login screen. Less fugly
    - Completely rewrote and redesign:
        - Register page
        - Login page
        - Reset password
        - Forgot password
- Updated Chatlogs and Xlrstats plugin to support new tables
- Started to replace all tables with bootstrap tables
- Removed `My account` from `footer.php`
- Removed `Logout button` from `footer.php`
- Removed `Help button` from `footer.php`
- Refactored the entire nav bar to use bootstrap
    - Fixed up the flow of the menu
    - Added profile dropdown
    - Cleaned up drop downs
    - All responsive now :)
- Removed Roboto font
    - Bootstrap has replaced it.
- Removed fontAwesome ( what a short life )
- Fixed absolute path for fav-icon
- Refactored header and footer
- Added Bootstrap files

Sun, `10th July 2016`
- Still so much work to be done.
- Fixed some indentation in `functions.php`
- Removed Punkbuster GUID link for CoD 1, 2, 4. PB no longer supports these games.
- Added Fuzzy GUID searching to `clients.php`
- Changed `Database.php` to `LegacyDatabase.php`
    - This is to make room for the PDO conversion
    - Added support for different Database Types
- Updated fav icon to make the new logo although in black this time
- Added Pagination Class file
    - Create Lazy Pages ( only next and back )
    - Create Non Lazy Pages ( numbers, next and back )
- Refactored all Class names
- Attempted to add pagination support to plugin
    - Attempted but failed.
    - Going to write a better system for it.
- Fixed weird bug where for some reason it would remove the tool tip text
- Removed all conflicting font styles ( this is going to make a mess :)
- Removed the more flexible search query for efficiency reasons
- Change input to be much less... gradienty
- More nav improvements
    - Added drop down arrow
    - Added dropdown menu arrow
- Moved `lib\log.txt` to `app\.bin`
    - Fixed `config.txt` to support the new location
- Moved `lib\plugins` to `app\plugins`
    - Fixed all dependency on the old folder
- Continued to fix Scss issues

Sat, `9th July 2016`
- Added new logo to Echelon
    - It's the same one from Echelon v3
- Change footer color
- Updated colorbox to support new version jQuery
- Added fontAwesome to the project
- Removed `geoip.php` heading comment ( think its messing with github )
    - Moved the file over to `app\vendor`
- Sorted `master.scss`
- Changed `header.php:83-97` to use `foreach` and not a `for` loop
- Fixed some images links
- Moved the navigation and profile to the very top of the page
    - Restyled the nav options
        - New drop downs
        - New animations
    - Restyled Profile area
        - Removed `last seen`
        - Added nicer icon for logout
- Applied new font to all elements
- Added new `roboto` font to `header.php`
- Removed `unitpngfix.js` no more support for < IE9
- Fixed all relations to old `images`, `styles`, `js` links
- Moved `images`, `styles`, `js` to `app\assets`
- Changed `functions.php:385` `css_file` to support new `styles` directory
- Sorted out `master.scss` to specific component files in `styles\components`
- Renamed `css` to `styles`
- Started cleaning up `master.scss`
- Moved navigation to the top of the page
- Converted `styles.css` and `styles.css` to SCSS format `css\master.scss`
- Changed css loading to min generated SCSS file `master.scss`
- Fixed line `functions.php:711` to support PHP7
