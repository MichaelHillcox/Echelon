# Change Log
#### Versions Types: Alpha | Beta | Release / Stable

## Version 3.0.0a

### TODO:
> Things that need doing before this versions release
- Improve the Follows plugin
    - This needs Pagination
    - And orderin
- Integrate the chatlogs plugin to the main echelon system
    - Improve the Chat logs plugin
    - This needs the ability to have pagination
- Fix the hack detection crap. Currently simply commented it out on line `364` in `functions.php` `hack()`
- Look at writing an api
---

#### Friday, `19th Jan, 2018`
> It's been a while. here is some of the things I've been working on since I last updated this changelog
### Added
- Added a very basic route system
    - Along with this I've fixed up a lot of things that go with it.
- Added a `bootstrap.php` file to help with keeping the core code in one place
- Added a `Echelon\Instance` class to help with keeping the Instance data in one place
- Started work on a `console.php` but as of now it's not functional

### Changed
- Updated the way some plugins work to work with the new routing system
- Moved the main `index.php` to `/public/index.php` to stop access to `app` and other directories.
    - This is also a security improvement

### Removed
- Removed `error-page.php`
- Removed some more of the cappy hack detection.

### Fixed
- Fixed a pretty big issue with password creation
- Fixed plugins not working with new routes system.
- Fixed a fuck lot of issues. Like a metric fuck ton.
- Fixed `error.php` issues with session data
- Fixed people not being able to add games with the new config system

### Security
- Moved the b3 database config to the `Config.php`
    - This is not the best solution but it's better than storing the password in plain text in the database
    - This means you will now have to reference the wiki for how to set games up.

#### Sun, `1st Oct, 2017`
> Back dating some changes and some new things
### Added
- A Plugin for showing players being watched / followed using the followed b3 plugin
    - This is very simple and needs improving

### Changed
- Improved the nav structure
- Plugins class has been greatly improved
    - Meaning that it's slowly becoming a lot simpler to make plugins for echelon!
- We no longer add B3 Database details inside of the the Echelon DB. These are now handled by our config file

### Removed
- Get Links from the homepage, This is pretty useless
    - Also removed from the sql structure.
- Hack detection actually being able to lock your account
    - This whole system needs refactoring and rethinking so the simplest thing to do is to disable it until I get the time to do so.

#### Sat, `27th May, 2017`
### Security
- For some reason we where not checking the current password in me.php
    - This has been fixed and we now no longer accept changes to any of the users details without the password first being verified.

#### Fri, `26th May, 2017`
### Added
- Added an `installed` constant to the config file.
- Added a fail-safe to the logger function.
- Added a `placeholder` file to keep `.bin` on the git repo
- Added a more robust way to make the log file
- You can now setup your admin account on the install
- Added `Fatal Error` page to make errors a little less scary.

### Changed
- Directory Structure changed completely
- Change the config template to be a little different.
- Updated update url a bit although this is not yet functioning.
- The update message now only shows on the main admin page
- Readme and Changelog file names.
- Moved the `.sql` file out of the main directory.
- We now open a profile tab by default on the profile pages.
- Moved `plugins` dir out of the `app` folder.
- We now show more logical game names instead of their shorter counter parts in the `settings-games.php` page
- Changed how write data to the config file.
- Completely changed the install page
    - Moved it over to bootstrap
    - Updated the html spec
    - Added a fav icon
    - Cleaned up the layout
- Updated errors to use bootstrap alerts
- Moved everything out of `inc` to the new directory structure.
- Made the game nav use nicer `b3 game` names

### Removed
- Removed a deprecated php function
- Removed `plugins` heading from the nav if the `b3 game` does not contain any plugins

### Fixed
- Database issue where data wouldn't be able to insert.
- A big issue with the navigation bar.
    - It wouldn't show anything if the user had not yet set up a b3 database. Silly mistake while reskinning the site.
- Not being able to ban people due to a `_POST` data error
- Plugins not showing up when editing a `b3` database
- Fixed lots more `_POST` checking issues from the old developer.
- Fixed not being able to edit `b3 games`
- Fixed `install` no longer sending an email if you required it.
- Fixed all ( known ) issues with the install page.
- `install` not verifying if the db password had actually been entered
- Fixed Chatlog not working due to incorrect paths.

### Security
- Slight improvement.
    - We now encode the password from `install`.
- You can no longer try and install again.

## Alpha 2.1.0.1 Dev Stage
#### Fri, `24th Feb 2017`
> Jesus, I suck at maintaining this project, Sorry people.

### Changed
- Spelling fixed in `header.php` from good to go. @wassie #5
- Changed all `echelon.bigbrother.net` links to their github/wiki counterparts @wassie #4

### Fixed
- Fixed an hacking attempt introduced but the addition of a button type... @efinst0rm #9

## OLD FORMATTING!
---
#### Sat, `30th October 2016`
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
    - Add Screen
        - Updated interface
        - Moved username and password around
        - General clean up
- Rewrite of `settings-server.php`
    - Updated Interface on all views
    - Improved usability
    - Added ability to edit the game a server is attached to #feature
    - Updated `actions/settings-server` and `Legacy Database.php` to reflect edit abilities
- Rewrite of `sa.php`
    - Improved main screen
        - turned each section into a tab to stop the infa scroll issue
        - Cleaned up the design
        - Fixed up some issues with placeholders
    - Cleaned up Group view
        - Changed out shitty navs for nice ones
        - Updated heading
- Small cleaning
    - `active.php` header cleaned up
    - `regular.php` header cleaned up
    - `admin.php` header cleaned up
    - `notices.php` header cleaned up
    - Chat Logger
        - Updated header
        - Cleaned up checkbox
    - Clients
        - Cleaned up search area
        - Cleaned up heading message
    - Improved Navigation flow a little bit
- Begun code from new homepage game selector


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


# Notes: Ignore this part
## {Build kind} {Build Number} {Release Stage}
{Day in text}, `{Day Month, Year}`
> {Update comment here}

### Added
-

### Changed
-

### Deprecated
-

### Removed
-

### Fixed
-

### Security
-
