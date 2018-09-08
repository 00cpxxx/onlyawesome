<?php
/* Only Awesome - A Font Awesome reducer to generate smaller JS font files based only in what was found.
 * 
 * This code was done in a few hours, uses poor mechanics, and should not be used by anyone.
 * It assumes good HTML that uses quotes around attributes, bad HTML may explode your device.
 * It also assumes that after the first fal, fab, fas or far is the icon name, so if you use modifiers they must come after the icon.
 * This script was based on all.js from FontAwesome 5.2, if the way they store icons change you are doomed.
 * Most likely 3 lines of grep wizardry would be able to do all this.
 * Aproximately one billion improvements could be made to get better performance in this script, I use it on sites of at most 100 files so I don't care.
 *
 * When finished you will have a single JS file called onlyawesome.js with all icons used in your site.
 * 
 * PUBLIC DOMAIN, NO WARRANTY, USE AT YOUR OWN RISK
 *
 * Bruno Jesus - 00cpxxx@gmail.com - 09/2018
 */

$FILE_PATTERN      = '*.{html,php}';

$YOUR_FILES_PATH   = '/xxxxx/'; //must have / in the end

$FONT_AWESOME_PATH = '/yyyy/'; //must have / in the end

$RECURSIVE_SEARCH = true; //look into subfolders

// =============================================================================

if(!is_dir($YOUR_FILES_PATH))
  die('Please edit this file specifying a folder with the files to check.'.PHP_EOL);

$EXPECTED = ['solid.js', 'regular.js', 'light.js', 'brands.js']; //do not reorder - see $fa array
$MAIN_FILE = $FONT_AWESOME_PATH.'fontawesome.js';

if(!is_dir($FONT_AWESOME_PATH) || !file_exists($MAIN_FILE))
  die('Please edit this file specifying a valid folder where the Font Awesome and fontawesome.js files reside.'.PHP_EOL);

//patterns we look for inside source files
$fa = ['"fas ', '"far ', '"fal ', '"fab '];

$VALID_JS = [];
$ICONS_DATA = [];
$PRE_DATA = [];
$POST_DATA = [];
$found = 0;
foreach($EXPECTED as $k => $f)
{
  //look for the icon files we have
  if(file_exists($FONT_AWESOME_PATH.$f))
  {
    $ALLS[$found] = file($FONT_AWESOME_PATH.$f);
    $VALID_JS[$found] = $fa[$k];
    echo "Icon file found: ",$f,PHP_EOL;

    $ICONS_DATA[$found] = [];

    //extract the parts of the file before and after the icon set
    $BASE = implode(false,$ALLS[$found]);
    $ix = strpos($BASE, 'var icons = {');
    if(!$ix)
      die("It looks like the format of the Font Awesome file changed, could not find 'var icons' string.".PHP_EOL);

    $ex = strpos($BASE, '}', $ix);
    if(!$ex)
      die("It looks like the format of the Font Awesome file changed, could not find the block close for 'var icons'".PHP_EOL);

    //store for later use, if no icons of this set are detected it wil not be added
    $PRE_DATA[$found] = substr($BASE, 0, $ix + 14);
    $POST_DATA[$found] = substr($BASE, $ex);

    $found++;
  }
}

if(!$found)
  die('Could not find any icon files (eg solid.js, light.js...)'.PHP_EOL);

$ICONS = [];

//function that looks for font awesome classes using string search.
function AwesomeFile($f)
{
  $data = file_get_contents($f);
  if(!$data)
  {
    echo "Failed to open file $f",PHP_EOL;
    return;
  }

  //echo "Testing file $f",PHP_EOL;

  $ix = -1;
  foreach($GLOBALS['VALID_JS'] as $k => $kind)
    while(($ix = strpos($data, $kind, $ix + 1))!==false)
    {
      $ex1 = strpos($data, ' ', $ix + 5); //there could be other things like fa-2x
      $ex2 = strpos($data, '"', $ix + 5); //or just the icon name and attribute end
      $ex = min($ex1, $ex2);
      if(!$ex)
      {
        echo "Broken tag around offset $ix in file $f",PHP_EOL;
        return;
      }

      $name = substr($data, $ix + 5, $ex - $ix - 5);
      if(substr($name,0, 3) != 'fa-')
      {
        echo "Broken Font Awesome in file $f class '$name' does not start with 'fa-', around text: ", substr($data, $ix, 50),PHP_EOL;
        return;
      }
      AwesomeAppend($k, substr($name, 3));
    }
}

//function that looks for the icon inside the specified icon set and stores its data for later
function AwesomeAppend($k, $icon)
{
  if(in_array($k.$icon, $GLOBALS['ICONS'])) //ensure we don't have this icon for this set already
    return;

  $n = '  "'.$icon;
  $l = strlen($n);
  foreach($GLOBALS['ALLS'][$k] as $line)
  {
    if(substr($line, 0, $l) === $n)
    {
      $GLOBALS['ICONS'][] = $k.$icon;
      $GLOBALS['ICONS_DATA'][$k][] = $line;
      echo "Icon found: ",substr($GLOBALS['VALID_JS'][$k], 1, -1),' ',$icon,PHP_EOL;
      return;
    }
  }

  echo "Failed to find icon '$icon'",PHP_EOL;
}

//function that loops files and directories recursively
function AwesomeDir($d)
{
  //loop all files in the folder
  foreach(glob($d.$GLOBALS['FILE_PATTERN'], GLOB_BRACE) as $f)
    AwesomeFile($f);

  if($GLOBALS['RECURSIVE_SEARCH'])
    foreach(glob($d.'*', GLOB_ONLYDIR) as $d)
      AwesomeDir($d.'/');
}

AwesomeDir($YOUR_FILES_PATH);

//Patch a new file with only the required base file plus icons and structures from each icon type
$mix = file_get_contents($MAIN_FILE);
foreach($ICONS_DATA as $k => &$v)
{
  if(!empty($v))
    $mix .= $PRE_DATA[$k].implode(false, $v).$POST_DATA[$k];
}

if(!file_put_contents('onlyawesome.js', $mix))
  die('Failed to write output file onlyawesome.js to current directory.'.PHP_EOL);

echo 'Process finished! File onlyawesome.js created, icons found: ',count($ICONS),PHP_EOL;
