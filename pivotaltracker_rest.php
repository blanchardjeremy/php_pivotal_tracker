<?php
/**
 * Pivotal Tracker Rest (PHP)
 * pivotaltracker_rest.php - include this file to daisy-chain required classes
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

define('PTR_BASE', './'); // If you need an absolute path, use this to prepend

require_once(PTR_BASE.'interfaces/iPivotalTracker.interface.php');
require_once(PTR_BASE.'interfaces/iPivotalTracker.interface.php');

require_once(PTR_BASE.'classes/PivotalTracker.class.php');
require_once(PTR_BASE.'classes/PivotalTrackerRest.class.php');
