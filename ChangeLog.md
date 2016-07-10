# Change Log

Sun, `10th July 2016`
- Attempted to add pagination support to plugin
    - Attempted but failed.
    - Going to write a better system for it.
- Fixed weird bug where for some reason it would remove the tool tip text
- Removed all conflicting font styles ( this is going to make a mess :)
- Removed the more flexable search query for effiency reasons
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
