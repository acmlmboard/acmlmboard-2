
# Acmlmboard II
In this repository you will find the code for Acmlmboard II. This is the version that was forked off for use at Kafuka. Acmlmboard II is the second in a series of web forum software systems originally designed and coded by Acmlm. Over the years it has since been added to and maintained by passionate users and developers.

This software is as-is and has no guarantees. You may find some advice if you ask at Kafuka's forums or IRC channels. It is strongly recommended you know your way around PHP and MySQL before continuing as this is older software that is 'less than user friendly' at times.

Branches
-------
**stable** - This is the stable development branch. It is the most up to date, and generally stable enough to work fine without major problems. The branch contains a pretty much 'known working' snapshot of the software which we have tested in advance and use in production. Since this is also the development branch it is recommended you test it before using in your own production enviroment. This was created during our process to update to PHP7 and supersedes our develop branch. 

**master** - This is the branch is a snapshot of develop where most things are working and should install and run without any major issues.

**develop** - This is the main branch of developement. Currently is is extremly **UNSTABLE** and should not be used in production. We are currently in the process of adding PHP 7 support.

**experimental** - This branch was started by xenons and includes the start of fundamental improvements for the board. If you fork the board for extensive development I would recommend starting here and building on it.

Tags
-------
**v2.1** - Raw code that ran on board2 in August 2011.

**v2.5** - The initial code for Kafuka

**v2.5.1** - Point Release #1

**v2.5.2** - Point Release #2

**v2.5.3** - Point Release #3; Includes all fixes that were in the kafuka branch that did not make it to public. Newly genned/rebased main.sql.

**v2.5.4** - Point Release #4; Lots of minor fixes and enhancements. Stable updated to 2.5.4.

Note: This board is not guaranteed to work out of the box. Not much support remains these days. You will need to get your hands dirty.
Please feel free to fork this and improve on it!
# Installing Acmlmboard II
This document will guide you in the process to installed ABII.

Requirements
------------
These requirements are based on our development environment's specifications. While older versions may work since the code is mostly wrote for 2005, our new components take advantage of more modern functioning. It is recommended you use the requirements provided for best results.

- Apache httpd ver >= 2.2.21 (Should work on earlier versions)
- MySQL5+/MariaSQL (strict mode disabled)
- PHP v5.6+ for Stable Branch, PHP 7+ for Development branch.
- PHP-GD
- mcrypt and the module for PHP (Stable branch)
- OpenSSL and the module for PHP (Development branch)
- phpmyadmin or DB tool of your choice.

Installation
############
1. Clone the git repository. Make sure you are on **develop** (or download the tarball and extract it)
2. If you haven't done so, create a MySQL DB and create a MySQL user for the board to use. It is **required** that you provide the CREATE TEMPORARY TABLE privilege or the tag generator will not work.
3. Change to the public_html dir. You may need to change the permissions to allow your httpd to read the board files/directories. It is recommended to change the owner and group to your apache/httpd's user account/group. Make sure that themes_serial.txt is writable by the server. This file is the cache used for the theme system. Make sure the directories ./userpics and gfx/tags are present and are writable by the apache/httpd as well. (**Note:** if you install to user acmlmboard on a debian system, the chown will be "chown acmlmboard:www_data") As a general guide, chmod folders to 755 and files to 644. With Apache+MPM-ITK (or PHP-FPM), you can also assign the script to run as the user/group you want.
4. Using mysql a.k.a. The MySQL Command Line Interface (or GUI tool such as phpMyAdmin) you will need to load the schema + basic data (sql/main.sql) into your database. (mysql -u **dbuser** -p **database** < main.sql). 
5. If there are any optional features you wish to use (sprites, shop/items, more ranks, ip location, and more [See "Optional Databases"] you may load them afterwards using the same way you loading the main database.
6. In the public_html/lib/ dir you will find the location of the board configuration. copy or rename **config.sample.php** to **config.php**. Edit it in your favorite editor. See **Configuring ABII** for more details
7. Open up the board in your browser. Register your first user. In the future this user will automatically become **Root Administrator** via an interactive installer. 
8. **OPTIONAL/LEGACY STEP FOR OLDER VERSIONS**Open up your MySQL tool of choice, and open up the user table. Change your login's group_id to 6 and power to 4. This will make your account a member of the Root Admin group (6) and the legacy power level to 4 (root). (**NOTE:** As stated this is only needed for version between 2.5.1 and 2.5.2, however if you run into a strange permissions issue as a Root Administrator change this setting and if it work let the development team know.)
9. Use the Manage Forums link at the top to make create some catagories and some forums.
10. The Permissions system, hidden forums, and other actions will have to be manually set in the database. Please see **Managing Permissions Manually** (Coming soon)

Optional Databases
##################
- **ABranks.sql:** Contains all the Acmlmboard ranks. Including Mario, Zelda, Kirby, and Dots. 
- **ABrpg.sql:** Contains the database information  needed for Acmlm's RPG game from Acmlm's Board II. You likely won't need to import this.  
- **B2itemset.sql:** This contains all the RPG items used on Acmlm's Board II, Board2, and Kafuka respecitively. If you wish to use the item system you may wish import this as the default database loads a place holder item.
- **badges.sql:**  Contains all the badges used at Kafuka as of now. The graphics are included if you decide to use these badges. No badges are included on the default database.
- **ip2c.sql:**  Contains all the ip to country information. It's recommended to load this when you setup your board, but it is not required. 
- **robots.sql:** Contains a default set of known web bots/spiders. This is not required, but is recommended to load this when you setup your board.
- **sprites.sql:**  Contains the spriteset used at Kafuka. If you wish you use sprites you may want to use the set. If you don't wish to use sprites, or to provide your own than you do not need this. The sprite files from Kafuka are included. The default database does not contain any sprites.
