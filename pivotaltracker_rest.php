<?php

/*
* Author: Jeremy Blanchard auzigog@gmail.com
*/

class PivotalTracker {
	
	var $username = '';
	var $password = '';
	var $token = '';
	
	var $rest;
	
	
	function __construct($token = null) {
		$this->rest = new PivotalTrackerREST($token);
	}
	
	// Helper function for quick authentication
	function authenticate() {
		$this->rest->username = $this->username;
		$this->rest->password = $this->password;
		$token = $this->rest->authenticate();
		return $token;
	}
	
	function projects_get($project_id = null) {
		$function = 'projects';
		if(!empty($project_id))
			$function = $function.'/'.$project_id;
			
		$arr = $this->_execute($function);
		return $arr;
	}
	
	function memberships_get($project_id, $memberships_id = null) {
		$function = 'projects/'.$project_id.'/memberships';
		if(!empty($memberships_id))
			$function = $function.'/'.$memberships_id;
		
		$arr = $this->_execute($function);
		return $arr;
	}
	
	function stories_get($project_id, $story_id = null){
		$function = 'projects/'.$project_id.'/stories';
		if(!empty($story_id))
			$function = $function.'/'.$story_id;
		
		$arr = $this->_execute($function);
		$stories = $arr['stories']['story'];
		return $stories;
	}
	
	function stories_get_by_filter($project_id, $filter) {
		$function = 'projects/'.$project_id.'/stories?filter=';
		$function .= urlencode($filter);
		
		$arr = $this->_execute($function);
		$stories = $arr['stories']['story'];
		return $stories;
	}
	
	/**
	* $project_id
	* $group = done, current, backlog, null 
	*/
	function iterations_get($project_id, $group = null) {
		$function = 'projects/'.$project_id.'/iterations';
		if(!empty($group))
			$function .= '/'.$group;
		
		$arr = $this->_execute($function);
		$iterations = $arr['iterations']['iteration'];
		return $iterations;
	}
	
	function activity_get($project_id = null) {
		$function = 'activities';
		if(!empty($project_id))
			$function = 'projects/'.$project_id.'/'.$function;
			
		$arr = $this->_execute($function);
		return $arr;
	}
	
	function _execute($function, $vars=null, $method='GET') {
		if(empty($this->rest)) {
			$this->rest = new PivotalTrackerREST();
		}
		if(empty($this->rest->token)) {
			$this->rest->token = $this->token;
		}
		$arr = $this->rest->_execute($function, $vars, $method);
		return $arr;
	}
}



class PivotalTrackerREST {
	
	
	var $base_url = '';
	
	var $force_ssl = false;
	
	var $username = '';
	var $password = '';
	
	var $token = null;
	
	function __construct($token = null) {
		$this->base_url = 'https://www.pivotaltracker.com/services/v3/';
		
		if(!empty($token))
			$this->token = $token;
		
	}
	
	function is_authenticated() {
		return !empty($this->token);
	}
	
	// Helper function to quickly authenticate
	function authenticate() {
		if(!$this->is_authenticated()) {
			$token_arr = $this->tokens_active($this->username, $this->password);
			
			$this->_store_authentication($token_arr);
		}
		
		return $this->token;
	}
	
	// Should really be in the other class, but it's needed for the authentication method
	function tokens_active($username, $password) {
		$auth = array(
				'username' => $username,
				'password' => $password,
			);
		
		$function = 'tokens/active';
		$token_arr = $this->_execute($function, null, 'GET', $auth);
		
		return $token_arr;
	}
	
	function _store_authentication($token_arr) {
		$this->token = $token_arr['token']['guid'];
		$this->user_id = $toke_arr['token']['id'];
	}
	
	function _execute($function, $vars=null, $method='GET', $auth=null) {
		$xml = $this->_curl($function, $vars, $method, $auth);
		
		$arr = $this->_process_xml($xml);
		return $arr;
	}
	
	function _process_xml($xml, 
                    $flattenValues=true,
                    $flattenAttributes = true,
                    $flattenChildren=false) {
		
		$result_arr = $this->xml2array($xml, 0);
		//print_rr(htmlentities($xml));
		//print_rr($result_arr);
		
		return $result_arr;
	}
	
	
	
	function _get($url, $vars=null) {
		$this->_curl($url, $vars, 'GET');
	}
	
	function _post($url, $vars) {
		$this->_curl($url, $vars, 'POST');
	}
	
	function _put($url, $vars) {
		$this->_curl($url, $vars, 'PUT');
	}
	
	function _delete($url, $vars) {
		$this->_curl($url, $vars, 'DELETE');
	}
	
	/*
	$function String the end of the URL for the function call. Example: 'tokens/active'
	$vars Array associate array for post data
	$method String HTTP method
	$auth Array associative array for username and password
	 */
	function _curl($function, $vars=null, $method='GET', $auth=null) {
		// Construct the full URL
		$url = $this->base_url.$function;
		
		
		$url = str_replace( "&amp;", "&", urldecode(trim($url)) );
		
		
		$fields = (is_array($vars)) ? http_build_query($vars) : $vars; 
		
		$ch = curl_init($url);
		
		// Follow redirects
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
		
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// Set the request type
		switch ($method) {
			case 'GET':
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields)));
				break;
			case 'PUT':	
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields))); 
				break;
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields)));
				break;
			default:
				//TODO
				break;
		}
		
		
		// Add the Pivotal Tracker token
		
		if(!empty($this->token)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-TrackerToken: ' . $this->token));
		}
		


		// add user authentication if necessary
		$do_auth = !empty($auth) && is_array($auth) && !empty($auth['username']) && !empty($auth['password']);
		if($do_auth) {
			curl_setopt($ch, CURLOPT_USERPWD, $auth['username'].':'.$auth['password']);
		}
		
		// force ssl if necessary
		// TODO: Maybe it should alway suse SSL??
		if($this->force_ssl || $do_auth) {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
		} else {
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		}
		
		// Warning: This will blindly accept any certificate (even self signed ones) and is essentially unsecure
		// TODO: Do real authentication. http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		
		
		// Debug stuff
		//$this->_rest_debug($ch);
		

        // $output contains the output string
		$output = curl_exec($ch);
		//print_rr($output);
		
		// $response contains the response HTTP headers
		$response = curl_getinfo($ch);
		//print_rr($response);
		
        // close curl resource to free up system resources
		if (curl_errno($ch))
			return curl_error($ch);
		else
			curl_close($ch);
		
		return $output;
	}
	
	function _rest_debug($ch) {
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		$response_data = curl_exec($ch);
		header('Content-Type: text/html');
		print_rr('response_data:');
		print_rr($response_data); // Request header
		print_rr('all curl info:');
		print_rr(curl_getinfo($ch)); // Response header
		die();
		
	}
	
	
	
	// Found at http://www.bin-co.com/php/scripts/xml2array/

	/**
	 * xml2array() will convert the given XML text to an array in the XML structure.
	 * Link: http://www.bin-co.com/php/scripts/xml2array/
	 * Arguments : $contents - The XML text
	 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
	 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance.
	 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure.
	 * Examples: $array =  xml2array(file_get_contents('feed.xml'));
	 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute'));
	 */
	function xml2array($contents, $get_attributes=1, $priority = 'tag') {
	    if(!$contents) return array();

	    if(!function_exists('xml_parser_create')) {
	        //print "'xml_parser_create()' function not found!";
	        return array();
	    }

	    //Get the XML parser of PHP - PHP must have this module for the parser to work
	    $parser = xml_parser_create('');
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, trim($contents), $xml_values);
	    xml_parser_free($parser);

	    if(!$xml_values) return;//Hmm...

	    //Initializations
	    $xml_array = array();
	    $parents = array();
	    $opened_tags = array();
	    $arr = array();

	    $current = &$xml_array; //Refference

	    //Go through the tags.
	    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
	    foreach($xml_values as $data) {
	        unset($attributes,$value);//Remove existing values, or there will be trouble

	        //This command will extract these variables into the foreach scope
	        // tag(string), type(string), level(int), attributes(array).
	        extract($data);//We could use the array by itself, but this cooler.

	        $result = array();
	        $attributes_data = array();

	        if(isset($value)) {
	            if($priority == 'tag') $result = $value;
	            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
	        }

	        //Set the attributes too.
	        if(isset($attributes) and $get_attributes) {
	            foreach($attributes as $attr => $val) {
	                if($priority == 'tag') $attributes_data[$attr] = $val;
	                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
	            }
	        }

	        //See tag status and do the needed.
	        if($type == "open") {//The starting of the tag '<tag>'
	            $parent[$level-1] = &$current;
	            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
	                $current[$tag] = $result;
	                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
	                $repeated_tag_index[$tag.'_'.$level] = 1;

	                $current = &$current[$tag];

	            } else { //There was another element with the same tag name

	                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
	                    $repeated_tag_index[$tag.'_'.$level]++;
	                } else {//This section will make the value an array if multiple tags with the same name appear together
	                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
	                    $repeated_tag_index[$tag.'_'.$level] = 2;

	                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
	                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                        unset($current[$tag.'_attr']);
	                    }

	                }
	                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
	                $current = &$current[$tag][$last_item_index];
	            }

	        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
	            //See if the key is already taken.
	            if(!isset($current[$tag])) { //New Key
	                $current[$tag] = $result;
	                $repeated_tag_index[$tag.'_'.$level] = 1;
	                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

	            } else { //If taken, put all things inside a list(array)
	                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

	                    // ...push the new element into that array.
	                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

	                    if($priority == 'tag' and $get_attributes and $attributes_data) {
	                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++;

	                } else { //If it is not an array...
	                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
	                    $repeated_tag_index[$tag.'_'.$level] = 1;
	                    if($priority == 'tag' and $get_attributes) {
	                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

	                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
	                            unset($current[$tag.'_attr']);
	                        }

	                        if($attributes_data) {
	                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
	                        }
	                    }
	                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
	                }
	            }

	        } elseif($type == 'close') { //End of tag '</tag>'
	            $current = &$parent[$level-1];
	        }
	    }

	    return($xml_array);
	}
}
	

?>