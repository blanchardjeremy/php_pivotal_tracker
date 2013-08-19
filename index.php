<?php
/**
 * Pivotal Tracker Rest (PHP)
 * index.php - for quick testing
 * 
 * This fork:
 * @author David Christian Liedle <david.liedle@gmail.com>
 * 
 * Based on Original:
 * @author Jeremy Blanchard <auzigog@gmail.com>
 * 
 * @license http://opensource.org/licenses/MIT MIT License
 * 
 */

$body = 'No body content set.';

require_once('pivotaltracker_rest.php');
// should pull classes/* and interfaces/*
// Provides:
// - PivotalTracker class
// - PivotalTrackerRest class

/*
 * @todo Do something here, echo it out as $body
 */

?><!DOCTYPE html>

<html>
    <head>
        <title>PivotalTracker REST (PHP) TEST PAGE</title>
    </head>
    <body>
        <?=$body?>
    </body>
</html>
