# WebReporterPHP
The web component of WebReporter
Find the plugin component here: https://github.com/bradyrussell/WebReporterPlugin/

Installation instructions:

While I compose more detailed instructions please bear with these:

Temporary beta configuration:

Download the repository and move the /reports directory to be served by your webserver. Edit the configuration in /reports/lib/ReportsDatabase.php (to be moved to a separate CONFIG.inc file soon) filling in your database credentials. 

If you wish to use the dynmap link integration, navigate to the bottom of ReportsDatabase.php , editing line 306 from 

function LocationToDynmapLink($location, $map_url = "http://jahcraft.vip/map/")

to be 

function LocationToDynmapLink($location, $map_url = "http://YOURSITE.TLD/DYNMAP_DIRECTORY/").

All of this will be moved to a more convenient config file soon.

Don't forget to import the database SQL file!

For all version 0.x beta builds of WebReporter the database structure will remain compatible. As we move to 1.x it will probably change.

Import the SQL file found here:

https://github.com/bradyrussell/WebReporterPHP/blob/master/webreporter-v0.sql

By saving it, then running in the console:
mysql -u username -p < /path/to/webreporter-v0.sql

