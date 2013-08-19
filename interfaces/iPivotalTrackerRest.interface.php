<?php
/**
 * Pivotal Tracker Rest (PHP)
 * iPivotalTrackerRest - interface for the PivotalTrackerRest class
 * 
 * This fork:
 * @author David Christian Liedle <david.liedle@gmail.com>
 * 
 * Based on Original:
 * @author Jeremy Blanchard <auzigog@gmail.com>
 * 
 * @license http://opensource.org/licenses/MIT MIT License
 * 
 * 
 * xml2array function in this file by Binny V A <binnyva@gmail.com>
 * @link http://www.bin-co.com/php/scripts/xml2array/
 * 
 */

interface iPivotalTrackerRest {
    
    public function __construct( $token = null );
    
    public function is_authenticated();
    public function authenticate();
    public function tokens_active( $username, $password );
    /* @todo Right after the above function the code has a method with an underscore
     * We need to evaluate if that convention was used to indicate protected/private
     * methods and follow suite both with the declaration type and exclusion from
     * the interface if not public
     */
    
    public function xml2array( $contents, $get_attributes = 1, $priority = 'tag');
    
} // End of interface iPivotalTrackerRest
