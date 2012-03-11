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

Configuration
#############
ABII was developed starting in 2005, so most of the code is designed for an earlier style of programing. As such a few configuration changes are required to get it to run properly.

Apache VHost
------------
When you setup your VHost for the board you will be required to make the following setting changes:

    AllowOverride All
Also you will need register_globals on as well. It's recommended to do it in the .htaccess file:

	php_flag register_globals on
However you can set it globally if you so desire

**STUB**