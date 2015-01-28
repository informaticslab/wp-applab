#!/bin/sh

# WP4 VM location
dest='/var/www/html/applab'

# WP3 VM location
#dest='/var/www/html/wordpress'

# Greg's wordpress test vagrant
#dest='/Users/jtq6/vagrants/vagrantpress/wordpress'

temp='dw-focus'
#temp='twentytwelve'


# app-release plug-in files
cp ~/informaticslab/wp-applab/wp-content/plugins/app-release/app-release.php $dest/wp-content/plugins/app-release
cp ~/informaticslab/wp-applab/wp-content/plugins/app-release/release-mgr.php $dest/wp-content/plugins/app-release
cp ~/informaticslab/wp-applab/wp-content/plugins/app-release/mobile_apps_wordpress.php $dest/wp-content/plugins/app-release
cp ~/informaticslab/wp-applab/wp-content/plugins/app-release/gen_manifests.php $dest/wp-content/plugins/app-release


# template files for DesignWall Focus themes
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/single-app_release.php $dest/wp-content/themes/$temp
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/archive-app_release.php $dest/wp-content/themes/$temp
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/index.php $dest/wp-content/themes/$temp
cp ~/informaticslab/wp-applab/wp-content/themes/$temp/style.css $dest/wp-content/themes/$temp
