# WP Cloudflare Dashboard #
[![Build Status](https://travis-ci.org/tpkemme/wp-cloudflare-dashboard.svg?branch=master)](https://travis-ci.org/tpkemme/wp-cloudflare-dashboard)

**Contributors:**      Tyler Kemme  
**Requires at least:** 4.4  
**Tested up to:**      4.7.2
**Stable tag:**        0.3.6  
**License:**           GPLv2  
**License URI:**       http://www.gnu.org/licenses/gpl-2.0.html  

## Demo ##

![gif_demo](https://github.com/tpkemme/wp-cloudflare-dashboard/blob/master/assets/repo/wp-cloudflare-dashboard.gif)
## Description ##

A Cloudflare Analytics Dashboard for Wordpress.  Simply enter your Cloudflare Email Address and Cloudflare API Key and you can access the analytics dashboards for each of your sites on Cloudflare.

## Installation ##

### Installation through Wordpress ###

1. Download the [latest release](https://github.com/tpkemme/wp-cloudflare-dashboard/releases/latest)

2. Upload the entire `/wp-cloudflare-dashboard` directory to the `/wp-content/plugins/` directory OR upload the plugin using the Wordpress 'Plugins' menu.

3. Activate WP Cloudflare Dashboard through the 'Plugins' menu in WordPress.

### Installation for Developers ###

1. Clone the repository to `/wp-content/plugins`
	
	`git clone https://github.com/tpkemme/wp-cloudflare-dashboard.git`

2. Install dependencies with composer and npm
	
	`composer install && npm install --only=dev`

## Contributing ##

Use the `npm version` command to increment the version number for the plugin across all files.  The command `composer dist` can be used to create a new 'dist' branch.  The 'dist' branch contains only the files necessary for the functionality of the plugin. All development files are removed from the 'master' branch to create the 'dist' branch.

The typical developer's workflow looks something like this:

1. Add and push any changes made to the repo

	`git add -A && git commit -m "example commit"`

2. Update the version

	`npm version minor && git push origin master`

3. Create a new distribution release

	`composer dist`

## Screenshots ##

![img_screenshot](https://github.com/tpkemme/wp-cloudflare-dashboard/blob/master/assets/repo/wp-cloudflare-screenshot.png)
