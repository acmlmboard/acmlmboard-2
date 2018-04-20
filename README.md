
# Acmlmboard II
In this repository you will find the code for Acmlmboard II. This is the version that was forked off for use with the web board that powers Kafuka.

This software is as-is and has no gurentees.

Branches
-------
**develop** - This is the main branch. It is the most up to date and usually stable. Since this is also the development branch it is recommended you test before using on live.

**stable** - This is the branch is a snapshot of develop where most things are working and should install and run without any major issues.

**experimental** - This branch was started be xenons and includes the start of fundamental improvements for the board. If you fork the board I would recommend starting here and building on it.

Tags
-------
**v2.1** - Raw code that ran on board2 in August 2011.

**v2.5** - The initial code for Kafuka

**v2.5.1** - Point Release #1

**v2.5.2** - Point Release #2

**v2.5.3** - Point Release #3; Includes all fixes that were in the kafuka branch that did not make it to public. Newly genned main.sql.

**v2.5.4** - Point Release #4; Lots of minor fixes and enhancements. Stable updated to 2.5.4.

Note: This board is not guaranteed to work out of the box. Not much support remains these days. You will need to get your hands dirty.
Please feel free to fork this and improve on it!
# Installing Acmlmboard II
This document will guide you in the process to installed ABII.

Requirements
------------
These requirements are based on our development environment's specifications. While older versions may work since the code is mostly wrote for 2005, our new components take advantage of more modern functioning. It is recommended you use the requirements provided for best results.

- Apache httpd ver >= 2.2.21 (Should work on earlier versions)
- MySQL5+/MariaSQL
- PHP >=v5.6 (old versions may work, but are unsupported.**Note:** As of commit f92315e on develop 5.6 is **REQUIRED** )
- PHP-GD
- mcrypt and the module for PHP
- phpmyadmin or DB tool of your choice.

Installation
############
1. Clone the git repository. Make sure you are on **develop** (or download the tarball and extract it)
2. If you haven't done so make a MySQL DB and create a MySQL user for the board to use. It is **required** you provide this use CREATE TEMPORARY TABLE privilege or the tag generator will not work.
3. change to the public_html dir. You may need to change the permissions to allow your httpd to read the board. It is recommended to change the owner and group to your httpd's user account/group. Make sure that themes_serial.txt is writable by the server. This file is the cache used for the theme system. Make sure the directories ./userpics and gfx/tags are present and are writable by the httpd as well. 
4. Using mysql (or phpmyadmin) you will need to load the schema + basic data (sql/main.sql) into your database. (mysql -u **dbuser** -p **database** < main.sql). 
5. If there are any optional features you wish to use (sprites, shop/items, more ranks, ip location, and more [See "Optional Databases"] you may load them now the same way you loading the main database.
6. In the public_html/lib/ dir you will find the location of the board configuration. copy or rename **config.sample.php** to **config.php**. Edit it in your favorite editor. See **Configuring ABII** for more details
7. Open up the board in your browser. Register your first user. In the future this user will automatically become **Root Administrator** via an interactive installer. 
8. Open up phpmyadmin (or other mysql tool) and open up the user table. Change your login's group_id to 6 and power to 4. This will make your account a member of the Root Admin group (6) and the legacy power level to 4 (root).
9. Use the Manage Forums link at the top to make come catagories and some forums.
10. The Permissions system, hidden forums, and other actions will have to be manually set in the database. Please see **Managing Permissions Manually**

Optional Databases
##################
- **ABranks.sql:** Contains all the Acmlmboard ranks. Including Mario, Zelda, Kirby, and Dots. 
- **ABrpg.sql:** Contains the database information  needed for Acmlm's RPG game from Acmlm's Board II. You likely won't need to import this.  
- **B2itemset.sql:** This contains all the RPG items used on Acmlm's Board II, Board2, and Kafuka respecitively. If you wish to use the item system you may wish import this as the default database loads a place holder item.
- **badges.sql:**  Contains all the badges used at Kafuka as of now. The graphics are included if you decide to use these badges. No badges are included on the default database.
- **ip2c.sql:**  Contains all the ip to country information. It's recommended to load this when you setup your board, but it is not required. 
- **robots.sql:** Contains a default set of known web bots/spiders. This is not required, but is recommended to load this when you setup your board.
- **sprites.sql:**  Contains the spriteset used at Kafuka. If you wish you use sprites you may want to use the set. If you don't wish to use sprites, or to provide your own than you do not need this. The sprite files from Kafuka are included. The default database does not contain any sprites.
