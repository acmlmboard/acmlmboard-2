# Acmlmboard II
In this repository you will find the code for Acmlmboard II. This is the version that was forked off for use at Kafuka. Acmlmboard II is the second in a series of web forum software systems originally designed and coded by Acmlm. Over the years it has since been added to and maintained by passionate users and developers.

This software is provided as-is and has no guarantees, however general advice can be received at the Kafuka forums. It is strongly recommended you know your way around PHP and MySQL before continuing, otherwise this probably isn't for you.
## Versioning
### Main Branches
**develop** - This is the main branch. It is the most up to date, and generally stable enough to work fine without major problems. Since this is also the development branch, it is still recommended that you test before making changes in production. 

**stable** - This is the branch which contains a pretty much 'known working' snapshot of the software which works on the configurations we tested it on at the time.

**experimental** - This branch was started by xenons and includes the start of fundamental improvements for the board. If you fork the board I would recommend starting here and building on it.

### Tags
**v2.1** - Raw code that ran on board2 in August 2011, which was used to form the basis for the AB2 project.

**v2.5** - The initial code for Kafuka.

**v2.5.1** - Point Release #1

**v2.5.2** - Point Release #2

**v2.5.3** - Point Release #3; Includes all fixes that were in the Kafuka branch that did not make it to the public previously.

**v2.5.4** - Point Release #4; Lots of minor fixes and enhancements. Stable updated to 2.5.4.

### Notice
This software is not guaranteed to work out of the box. Not much support remains these days, and generally speaking the target audience for this software are users with reasonable PHP knowledge. This does not mean we discourage any changes to make things easier, but do keep in mind that we aren't targeting the same people as those like phpBB, Discourse etc.

## Installation
This document will guide you through the process of installing AB2.

### Requirements
These requirements are based on our development environment's specifications. While older versions may work since the code is mostly wrote for 2005, our new components take advantage of more modern functioning. It is recommended you use the requirements provided for best results.

- Apache httpd ver >= 2.2.21 (Should work on earlier versions)
- MySQL 5.x/MariaDB (strict mode disabled)
- PHP 5.6 (old versions may work, but are unsupported. **Note:** PHP 7 support is currently *experimental*.)
- PHP-GD
- mcrypt and the module for PHP
- phpmyadmin or DB tool of your choice.

### Installation

1. Clone the git repository. Make sure you are on **develop** (or download the tarball and extract it)
2. If you haven't done so, create a MySQL DB and create a MySQL user for the board to use. It is **required** that you provide the CREATE TEMPORARY TABLE privilege or the tag generator will not work.
3. Change to the public_html dir. You may need to change the permissions to allow your httpd to read the board files. As a general guide, chmod folders to 755 and files to 644. With Apache+MPM-ITK (or PHP-FPM), you can also assign the script to run as the user/group you want.
4. Using the MySQL command line (or GUI tool such as phpMyAdmin), you will need to load the schema + basic data (sql/main.sql) into your database. (mysql -u **dbuser** -p **database** < main.sql). 
5. If there are any optional features you wish to use (sprites, shop/items, more ranks, IP location, and more [See "Optional Databases"] you may load them afterwards using the same method to import the main database.
6. In the public_html/lib/ dir you will find the location of the board configuration. Copy or rename **config.sample.php** to **config.php**. Edit it in your favorite editor. See **Configuring ABII** for more details.
7. Open up the board in your browser. Register your first user. In the future this user will automatically become **Root Administrator** via an interactive installer. 
8. Open up phpmyadmin (or other MySQL tool) and open up the user table. Change your account's group_id to 6 and power to 4. This will make your account a member of the Root Admin group (6) and the legacy power level to 4 (root).
9. Use the Manage Forums link at the top to create some categories and some forums.
10. The Permissions system, hidden forums, and other actions will have to be manually set in the database. Please see **Managing Permissions Manually**

### Optional Databases
- **ABranks.sql:** Contains all the Acmlmboard ranks. Including Mario, Zelda, Kirby, and Dots. 
- **ABrpg.sql:** Contains the database information  needed for Acmlm's RPG game from Acmlm's Board II. You likely won't need to import this.  
- **B2itemset.sql:** This contains all the RPG items used on Acmlm's Board II, Board2, and Kafuka respecitively. If you wish to use the item system you may wish import this as the default database loads a place holder item.
- **badges.sql:**  Contains all the badges used at Kafuka as of now. The graphics are included if you decide to use these badges. No badges are included on the default database.
- **ip2c.sql:**  Contains all the ip to country information. It's recommended to load this when you setup your board, but it is not required. 
- **robots.sql:** Contains a default set of known web bots/spiders. This is not required, but is recommended to load this when you setup your board.
- **sprites.sql:**  Contains the spriteset used at Kafuka. If you wish you use sprites you may want to use the set. If you don't wish to use sprites, or to provide your own than you do not need this. The sprite files from Kafuka are included. The default database does not contain any sprites.
