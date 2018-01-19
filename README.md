# `Echelon 3`
Echelon 3, a smarter way to manage and moderate clients in a RCON server.

## About
`Echelon 3` is a complete overhaul of the Design, UI and the UX (to a smaller extent) from Echelon 2 which was looking pretty dated. Echelon at it's core is a simple and easy to use online application that allows system administrators and (or) server administrators to have a quick way to proform moderation tasks on a Rcon based, B3 supported, server. 

## Features
Out of the box it comes with support for a [large array of Rcon based games](https://github.com/MichaelHillcox/Echelon/wiki/Supported-Games), Client moderation, In-Game chat support, [XLR Intergration](http://www.xlrstats.com/), User Permissions and a public ban list.

### Current Status:
##### Current Version `3.0.0a1` : `very-unstable`
Never use this code on a production server, I can not stress this enough. Until I've finished off my first and most important round of testing and security checks I can not pass this version off as being in anyway stable or secure. There is a lot of old code that still needs loving care and attention and I'm doing my best to get to it all.

You can find a detailed [Change Log](CHANGELOG.md) of all my little edits here and there. Please note this is a developers changelog and is not indented for non-developers to understand fully. 

> All of the below information is subject to change and will likely be moved to a wiki page instead. As it stands I'm writing wiki pages for all of the major changes in Echelon so feel free to [go and have a look](https://github.com/MichaelHillcox/Echelon/wiki)

### Installation
This is by no means a comprehensive guide, it is a quick guide to get any of you started

#### Requirements
- Webserver (Apache, Nginx)
- Version PHP 5.6 / 7 ( currently only 7 is supported. I'm working on supporting both ) 
- MySQL
    - A MySQL user with connection, read, modify and write permissions to your B3 databases

#### Steps
##### Database
- Create a MySQL user to connect your B3 database from your Webserver
- Run the echelon.sql file on your database to create the Echelon tables

##### Webserver Config
We run everything from the `/public` folder of our project structure. To help secure the instances you will need to change your webserver config to point at the `/public` folder in your echelon directory. To do this you'll need to do the following.
###### Nginx
```
// Change your root to the new /public 
server_name echelon.example.com;
root /webserver/echelon/public;
```
###### Apache
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

If you are using it in a subdirectory so something like example.com/echelon/ then you will need to change your Nginx or Apache config to make it so when you are at /echelon/ it will set the documentRoot or root( for nginx ) to /echelon/public/ By no means should you ever use example.com/echelon/public for the actual working website.

##### Install
- Go to your install path and follow the installer
- Select the box that says to delete the install folder
- Setup and config your Echelon to your needs

### Contributing
Fancy having a play? You're more than welcome to. I've got a lot of other projects on the go and this one tends to be left on the table so I'm always happy to see when others have some input or want to get down and dirty with the code. Feel free to Change, Remove, Fix or complain about anything in the code base :D

##### Current Contributors
[MichaelHillcox](https://github.com/MichaelHillcox) // 
[WickedShell](https://github.com/WickedShell),
[Specialkbyte](https://github.com/Specialkbyte),
[nathanthorpe](https://github.com/nathanthorpe),
[markweirath](https://github.com/markweirath)

### Tech being used
- *PHP 7* / 5.6
- MySQL
- jQuery
- Twitter Bootstrap
- Possibily others from old devs. 
