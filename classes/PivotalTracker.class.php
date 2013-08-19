<?php
/**
 * Pivotal Tracker Rest (PHP)
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

class PivotalTracker implements iPivotalTracker {
    
    // Public properties:
    public $token = '';
    public $rest;
    
        // Protected properties:
        // None at this time.
        
            // Private properties:
            private $username = ''; // @todo Set this to your username
            private $password = ''; // @todo Set this to your password
            
    /**
     * Constructor
     */
    public function __construct( $token = null ){
        
        $this->rest = new PivotalTrackerREST($token);
        
    } // End of public function __construct( $token = null )

    /**
     * Helper function for quick authentication
     * @return type
     */
    public function authenticate(){
        
        $this->rest->username = $this->username;
        $this->rest->password = $this->password;
        
        $token = $this->rest->authenticate();
        
        return $token;
        
    } // End of public function authenticate()
    
    /**
     * Get projects
     * 
     * @param type $project_id
     * @return type
     */
    public function projects_get( $project_id = null ){
        
        $function = 'projects';
        
        if( !empty($project_id) ){
            
            $function = $function.'/'.$project_id;
            
        } // End of if( !empty($project_id) )
        
        $arr = $this->_execute($function);
        
        return $arr;
        
    } // End of public function projects_get( $project_id = null )
    
    /**
     * Get memberships
     * 
     * @param type $project_id
     * @param type $memberships_id
     * 
     * @return type
     * 
     */
    public function memberships_get( $project_id, $memberships_id = null ){
        
        $function = 'projects/'.$project_id.'/memberships';
        
        if( !empty($memberships_id) ){
            
            $function = $function.'/'.$memberships_id;
            
        } // End of if( !empty($memberships_id) )

        $arr = $this->_execute($function);
        
        return $arr;
        
    } // End of public function memberships_get( $project_id, $memberships_id = null )
    
    /**
     * Get stories
     * 
     * @param type $project_id
     * @param type $story_id
     * 
     * @return array
     */
    public function stories_get( $project_id, $story_id = null ){
        
        $function = 'projects/'.$project_id.'/stories';
        
        if( !empty($story_id) ){
            
            $function = $function.'/'.$story_id;
            
        } // End of if( !empty($story_id) )

        $arr = $this->_execute($function);

        if( !empty($story_id) ){
            
            $stories = $arr['story'];
            
        } else {
            
            $stories = $arr['stories']['story'];
            
        } // End of if( !empty($story_id) ) / else
        
        return $stories;
        
    } // End of public function stories_get( $project_id, $story_id = null )
    
    /**
     * Get stories by filter
     * 
     * @param type $project_id
     * @param type $filter
     * 
     * @return type
     */
    public function stories_get_by_filter( $project_id, $filter ){

        $function  = 'projects/'.$project_id.'/stories?filter=';
        $function .= urlencode($filter);

        $arr = $this->_execute($function);
        
        $stories = $arr['stories']['story'];
        
        return $stories;
        
    } // End of public function stories_get_by_filter( $project_id, $filter )
    
    /**
     * Get iterations
     * 
     * @param type  $project_id
     * @param mixed $group done, current, backlog, null
     * 
     * @return type
     */
    public function iterations_get( $project_id, $group = null ){
        
        $function = 'projects/'.$project_id.'/iterations';
        
        if( !empty($group) ){
            
            $function .= '/'.$group;
            
        } // End of if( !empty($group) )
        
        $arr = $this->_execute($function);
        
        $iterations = $arr['iterations']['iteration'];
        
        return $iterations;
        
    } // End of public function iterations_get( $project_id, $group = null )
    
    /**
     * Get activity
     * 
     * @param type $project_id
     * 
     * @return array
     */
    public function activity_get( $project_id = null ){
        
        $function = 'activities';
        
        if( !empty($project_id) ){
            
            $function = 'projects/'.$project_id.'/'.$function;
            
        } // End of if( !empty($project_id) )

        $arr = $this->_execute($function);
        
        return $arr;
        
    } // End of public function activity_get( $project_id = null )
    
        /***********************************************************************
         * Protected Methods                                                   *
         **********************************************************************/
    
        /**
         * Execute
         * 
         * @param type $function
         * @param type $vars
         * @param type $method
         * 
         * @return type
         */
        protected function _execute( $function, $vars = null, $method = 'GET' ){

            if( empty($this->rest) ){

                $this->rest = new PivotalTrackerREST();

            }

            if( empty($this->rest->token) ){

                $this->rest->token = $this->token;

            }

            $arr = $this->rest->_execute($function, $vars, $method);

            return $arr;

        } // End of protected function _execute( $function, $vars = null, $method = 'GET' )
    
} // End of class PivotalTracker implements iPivotalTracker
