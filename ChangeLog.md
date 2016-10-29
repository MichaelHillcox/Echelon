# Change Log
---
### Version 2.1.0.1

Sat, `30th October 2016`
- More work on Client Details ( done for now ) 
- Began work on `settings.php`
    - More 'github' like settings feel ( thanks bootstrap :P )
    - Merged some settings
    - Removed Character Set
        - has been moved to `app/config`
- Moved `inc/config` to `app/config`
- Cleaned up `bans.php`, `kicks.php` and `pubbans.php`
    - Changed headers
    - Changed navigation to section the options out a bit more
- Improved pagination functionality 
    - It will show the next 3 if they exist when the page number is lower than 3
- Rewrite of `me.php`
    - Removed `me.js` no longer needed 
        - merged with site.js
    - Cleaned up inputs
    - Added page heading 
- Added handy little checkbox input toggler into `site.me`
- Fixing all those spelling mistakes 
- Rewrite of `settings-games.php`
    - Refactor game select logic 
    - Redesigned interface
    - Cleaned up some options

##### Changes
- Plugins now support descriptions

Fri, `29th October 2016`
- Continued work on Client Details page.
    - Worked on new js code
    - Cleaned up all the inputs under the new tabs
    - Fixed up broken `</div>`
    - Cleaned up chatlogs tables and navs.
        - using to many navs.. :P 
        
##### Changed
- Changed `tooltip` backend to use bootstrap

Wed, `26th October 2016`
`Note: Refer to commits for any missed items here... It's been a while and I've forgotten`
- Completely changed client details screen.
    - Moved client info to the a sidebar
    - Moved Main content into `.navs` items to clean up the look
    - Rewrote all of the forms on client details so they suck less. 
    - Removed `whois` link. That's just creepy


Tues, `19th September 2016`
- Updated table styles. Over time. 
    - Why is there so many damn tables :P 
    - Damn data
- Moved some things around

Thurs, `1st September 2016`
- Removed Current changelog from homepage
- Added a new way to add footer scripts
- Moved cd.php & me.php footer code out of `footer.php` to there own file

Tues, `30th August 2016`
- Added table styles to `sa.php` tables
- Updated layout on pubbans a bit.
- Decreased container width by `100px`
- Fixed Jumbotron issue with `.container`
    - bootstrap issue
- Enhanced homepage a little
- Fixed chatlogs layout to match bootstrap
- Updated table layout for chatlogs
- Updated table layouts to bootstrap for the rest of the tables
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
