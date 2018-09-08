# onlyawesome
A PHP script that searches source files and create a Font Awesome js file only with the icons found. Intended to reduce the download size.

# Description
Only Awesome - A Font Awesome reducer to generate smaller JS font files based only in what was found.

This code was done in a few hours, uses poor mechanics, and should not be used by anyone.

It assumes good HTML that uses quotes around attributes, bad HTML may explode your device.

It also assumes that after the first fal, fab, fas or far is the icon name, so if you use modifiers they must come after the icon.

This script was based on all.js from FontAwesome 5.2, if the way they store icons change you are doomed.

Most likely 3 lines of grep wizardry would be able to do all this.

Aproximately one billion improvements could be made to get better performance in this script, I use it on sites of at most 100 files so I don't care.

When finished you will have a single JS file called onlyawesome.js with all icons used in your site.

PUBLIC DOMAIN, NO WARRANTY, USE AT YOUR OWN RISK

# Usage
Open the PHP file and change the first 3 variables: $FILE_PATTERN, $YOUR_FILES_PATH, $FONT_AWESOME_PATH.

Run the script in console and watch the output.

#Example run
<pre>
bruno@Note2:~$ php onlyawesome.php 
Icon file found: solid.js
Icon file found: regular.js
Icon file found: light.js
Icon file found: brands.js
Icon found: fal arrow-to-bottom
Icon found: fal spinner
Icon found: fas phone
Icon found: far envelope
Icon found: fab twitter-square
Icon found: fab linkedin
Icon found: fab facebook-square
Process finished! File onlyawesome.js created, icons found: 7
</pre>
