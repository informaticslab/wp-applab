#!/bin/sh

#dest='/var/www/html'
dest='/Users/jtq6/vagrants/vagrantpress'

#temp='dw-focus'
temp='twentytwelve'


# app-release plug-in files
cp ~/informaticslab/wp-applab/wp-content/plugins/app-release/app-release.php $dest/wordpress/wp-content/plugins/app-release
cp ~/informaticslab/wp-applab/wp-content/plugins/app-release/release-mgr.php $dest/wordpress/wp-content/plugins/app-release

# template files for DesignWall Focus themes
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/single-app_release.php $dest/wordpress/wp-content/themes/$temp
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/archive-app_release.php $dest/wordpress/wp-content/themes/$temp
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/index.php $dest/wordpress/wp-content/themes/$temp
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/style.css $dest/wordpress/wp-content/themes/$temp

