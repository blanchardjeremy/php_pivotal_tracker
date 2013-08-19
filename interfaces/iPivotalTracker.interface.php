<?php
/**
 * Pivotal Tracker Rest (PHP)
 * iPivotalTracker - interface for the PivotalTracker class
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

interface iPivotalTracker {
    
    public function __construct( $token = null );
    
    public function authenticate();
    public function projects_get( $project_id = null );
    public function memberships_get( $project_id, $memberships_id = null );
    public function stories_get( $project_id, $story_id = null );
    public function stories_get_by_filter( $project_id, $filter );
    public function iterations_get( $project_id, $group = null );
    public function activity_get( $project_id = null );
    
} // End of interface iPivotalTracker
