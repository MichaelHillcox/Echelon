# Echelon 3
Echelon 3 is a smarter way to manage and moderate clients in a RCON server.

## About
Echelon 3 is a complete overhaul of Echelon 2's outdated Design, UI, and UX. Echelon is a simple to use online application which allows system and server administrators to have faster methods of performing moderating tasks on a Rcon based, B3 supported, server. 

## Features
Echelon 3 currently has support for sixteen [Rcon Games](https://github.com/MichaelHillcox/Echelon/wiki/Supported-Games) and provides features such as Client moderating, In-Game chat support, [XLR Intergration](http://www.xlrstats.com/), User Permissions, and a public ban list.

## Current Version `3.0.0a1` : `unstable`
The current version of Echelon 3 is not fully tested and people should never use the code on a production server. Until the most important round of testing and security checks have complete, version `3.0.0a1` of Echelon 3 will not pass off as stable or secure. There is a lot of old code that still needs improving and the development team is working hard to complete the tool.

A detailed [Change Log](CHANGELOG.md) of the progress for Echelon 3 shows the small changes for the tool. Please note this is a developers changelog and is not indented for non-developers to fully understand. 

> All of the information below is subject to change and will likely move to a wiki page instead. The [Wiki Pages](https://github.com/MichaelHillcox/Echelon/wiki) intend to display information for all of the major changes in Echelon 3 and progess on the development of the tool.

# Requirements
- Webserver (Apache, Nginx)
- Version PHP 7 and above 
- MySQL / Mariadb

# Installation
The installation guide is not comprehensive and only serves as a quick guide to start a project.

## Steps
### Database
- Create a MySQL user to connect the B3 database from your Webserver
- Run the echelon.sql file on the database to create the Echelon tables

### Webserver Config
Echelon 3 runs everything from the `/public` folder of the project structure. To help secure the instances, change the webserver config to point at the `/public` folder in the echelon directory by doing the following:

#### Nginx
```
// Change your root to the new /public 
server_name echelon.example.com;
root /webserver/echelon/public;
```

#### Apache
```
DocumentRoot "/webserver/echelon/public"
ServerName echelon.example.org
<Directory "/webserver/echelon/public">
    RewriteEngine on
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule . index.php [L]
</Directory>
```
Currently, Echelon 3 only works in the `/public` folder and not in a subdirectory. If your project requires a subdirectory for Echelon 3 then further research is required and please inform the author of any findings.

### Install
- Go to your install path and follow the installer
- Select the box that says to delete the install folder
- Setup and config your Echelon to your needs

# Contributing
Fancy having a play? You're more than welcome to. I've got a lot of other projects on the go and this one tends to be left on the table so I'm always happy to see when others have some input or want to get down and dirty with the code. Feel free to Change, Remove, Fix or complain about anything in the code base :D

# Contributors
[MichaelHillcox](https://github.com/MichaelHillcox) // 
[WickedShell](https://github.com/WickedShell),
[Specialkbyte](https://github.com/Specialkbyte),
[nathanthorpe](https://github.com/nathanthorpe),
[markweirath](https://github.com/markweirath)

# Licence 
[GNU General Public License v3.0](https://github.com/MichaelHillcox/Echelon/blob/master/LICENSE)
