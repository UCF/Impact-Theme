# UCF Impact Theme - [University of Central Florida Downtown Orlando, FL](http://www.ucf.edu/impact/)

WordPress theme built off of UCF's Generic theme for UCF's Student Impact, Access and Innovation Site.


## Installation Requirements:
* node v0.10.22+
* gulp v3.9.0+
* WordPress v4.1+


## Deployment
No special configuration should be necessary for deploying this theme.  Static assets that require minification and/or concatenation are tracked in the repo and should be pushed up as-is during deployment.


## Development
- Make sure an up to date version of node is installed
- Pull down the repo and `cd` into it.  Run `npm install` to install node packages in package.json, including gulp and bower.  Node packages will save to a `node_modules` directory in the root of the repo.
- Install all front-end components and compile static assets by running `gulp default`.  During development, run `gulp watch` to detect static file changes automatically and run minification and compilation commands on the fly.
- Make sure up-to-date concatenated/minified files are pushed up to the repo when making changes to static files.


## Important files/folders:

### functions/base.php
Where functions and classes used throughout the theme are defined.

### functions/config.php
Where Config::$links, Config::$scripts, Config::$styles, and
Config::$metas should be defined.  Custom post types and custom taxonomies should
be set here via Config::$custom_post_types and Config::$custom_taxonomies.
Custom thumbnail sizes, menus, and sidebars should also be defined here.

### functions.php
Theme-specific functions only should be defined here.  (Other required
function files are also included at the top of this file.)

### shortcodes.php
Where Wordpress shortcodes can be defined.  See example shortcodes for more
information.

### custom-post-types.php
Where the abstract custom post type and all its descendants live.

### static/
Where, aside from style.css in the root, all static content such as
javascript, images, and css should live.
Bootstrap resources should also be located here.


## Notes

This theme utilizes Twitter Bootstrap as its front-end framework. Bootstrap styles and javascript libraries can be utilized in theme templates and page/post content. For more information, visit http://twitter.github.com/bootstrap/

Note that this theme may not always be running the most up-to-date version of Bootstrap. For the most accurate documentation on the theme's current Bootstrap version, visit http://bootstrapdocs.com/ and select the version number found at the top of components/bootstrap-sass-official/bower.json.

### Using Cloud.Typography
This theme is configured to work with the Cloud.Typography web font service.  To deliver the web fonts specified in
this theme, a project must be set up in Cloud.Typography that references the domain on which this repository will live.

Development environments should be set up in a separate, Development Mode project in Cloud.Typography to prevent pageviews
from development environments counting toward the Cloud.Typography monthly pageview limit.  Paste the CSS Key URL provided
by Cloud.Typography in the CSS Key URL field in the Theme Options admin area.

This site's production environment should have its own Cloud.Typography project, configured identically to the Development
Mode equivalent project.  **The webfont archive name (usually six-digit number) provided by Cloud.Typography MUST match the
name of the directory for Cloud.Typography webfonts in this repository!**


## Custom Post Types
n/a


## Custom Taxonomies
n/a


## Shortcodes

### [blockquote]
* Generates a stylized blockquote.

### [posttype-list]
* Custom post types that have defined $use_shortcode as True can automatically
utilize this shortcode for displaying a list of posts created under the given
post type; e.g., [document-list] will output a list of all published Documents.
Additional parameters can be used to further narrow down the shortcode's results;
see the Theme Help section on shortcodes for an available list of filters.

### [search_form]
* Outputs the site search form.  The search form output can be modified via
searchform.php

### [post-type-search]
* Generates a searchable list of posts. Post lists are generated in alphabetical order and, by default, by category and post title. Posts can be searched by post title and any tags assigned to the post. See the Theme Help section on shortcodes for more information.
