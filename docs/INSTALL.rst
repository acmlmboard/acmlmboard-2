########################
Installing Acmlmboard II
########################
This document will guide you in the process to installed ABII.

Requirements
------------
These requirements are based on our development environment's specifications. While older versions may work since the code is mostly wrote for 2005, our new components take advantage of more modern functioning. It is recommended you use the requirements provided for best results.

- Apache httpd ver >= 2.2.21 (Should work on earlier versions)
- MySQL5
- PHP >=v2.3.0 (again old versions may work)
- PHP-GD
- mcrypt and the module for PHP
- phpmyadmin or DB tool of your choice.

Pre-Configuration
#################
ABII was developed starting in 2005, so most of the code is designed for an earlier style of programing. As such a few configuration changes are required to get it to run properly.

Apache VHost
------------
When you setup your VHost for the board you will be required to make the following setting changes:

    AllowOverride All
Also you will need register_globals on as well. It's recommended to do it in the .htaccess file:

	php_flag register_globals on
However you can set it globally if you so desire

Installation
############
1. Clone the git repository. Make sure you are on **develop** (or download the tarball and extract it)
2. If you haven't done so make a MySQL DB and create a MySQL user for the board to use. It is **required** you provide this use CREATE TEMPORARY TABLE privilege or the tag generator will not work.
3. change to the public_html dir. You may need to change the permissions to allow your httpd to read the board. It is recommended to change the owner and group to your httpd's user account/group. Make sure that themes_serial.txt is writable by the server. This file is the cache used for the theme system. Make sure the directories ./userpics and gfx/tags are present and are writable by the httpd as well. 
4. Using mysql (or phpmyadmin) you will need to load the schema + basic data (sql/main.sql) into your database. (mysql -u **dbuser** -p **database** < main.sql). 
5. In the public_html/lib/ dir you will find the location of the board configuration. copy or rename **config.sample.php** to **config.php**. Edit it in your favorite editor. See **Configuring ABII** for more details
6. Open up the board in your browser. Register your first user. In the future this user will automatically become **Root Administrator** via an interactive installer. 
7. Open up phpmyadmin (or other mysql tool) and open up the user table. Change your login's group_id to 6 and power to 4. This will make your account a member of the Root Admin group (6) and the legacy power level to 4 (root).
8. Use the Manage Forums link at the top to make come catagories and some forums.
9. The Permissions system, hidden forums, and other actions will have to be manually set in the database. Please see **Managing Permissions Manually**

**STUB + This needs to be less technical and more detailed**