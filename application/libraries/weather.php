<?php
/**
* Weather Script for CodeIgniter
*
* @author Kevin Burton [CoDeR]
* @package CodeIgniter 1.5.x
* @subpackage Libraries
* @version 1.1
*
* Thanks to Nick Schaffner from 53x11.com for the script.
* This has been modified and ported to CodeIgniter 1.5.4
*
* REQUIREMENTS
*
* 1. URL and DATABSE must be loaded.
* 2. Set the below paramters
* 3. By requirements of the weather.com API, you can only refresh the weather a minimum of 30 minutes.
* You can set this to more if you want by changing the cache_time value.
* 4. If you do not have the table setup already, the constructor will do it for you automatically.
* 5. You will need to register with weather.com and it will give you a folder of icons you use with
* the system.  This system will output the filename for it.
* 6. ENJOY!
*
* USAGE
*
* <?
* $config['zipcode'] = 'XXXXXXX';
* $config['partner'] = 'XXXXXXX';
* $config['license'] = 'XXXXXXX';
* $this->load->library('weather', $config);
* ?>
*
* Get the array by calling: $this->weather->get_weather();
* To spit out the contents of the array, use: $this->weather->trace_array();
*
* OR
*
* You can access the variables individually instead of through the get_weather() function
* Example: $this->weather->sunr; // Sunrise.
* or $this->weather->icon; // the icon name
*
*
*/

class Weather
{

    /* You must modify these variable to match your own */
    var $partner = '1113631629';
    var $license = '7d1ffe974dc20604';
    var $base_dir = './assets/weather/';
    var $wfile = './assets/weather/weather.xml';
    var $units = 's';
    var $table_name = 'weather';

    /* DO NOT MODIFY below: */
    var $xmlserver = 'http://www.google.com/ig/api?weather=';
    var $CI; // declare holder for CodeIgniter
    var $cache_time = 30; // 30 minutes requied for cache
    var $data_array = array();

    /**
    * PHP4 Constructor
    *
    * @return Weather
    */
    function Weather($params = array())
    {
        $this->CI =& get_instance(); // load instance of CodeIgniter
        $this->CI->load->helper('file'); // load File Helper functions
		$this->initialize($params);
    }

    /**
    * Class Initialization
    *
    * @access public
    * @param array $params
    * @return void
    */
    function initialize($params = array())
    {
        if( count($params) > 0 )
        {
            foreach ($params as $key=>$value)
            {
                $this->$key = $value;
            }
        }
        $this->_check_install();
    }
	
	function get_weather($zipcode = false)
    {
        $this->wfile = $this->base_dir . $zipcode . '.xml';
        return $this->_requestXML($zipcode);
    }

    /**
    * Trace method… Array and Object Friendly viewer
    *
    * @param unknown_type $obj
    * @param unknown_type $die
    */
    function trace($obj, $die=false)
    {
        print("");
        print_r($obj);
        print("</pre>");
        
        if($die)
        {
            die();
        }
    }

    /**
    * Check Installation
    *
    * @access private
    * @return void
    */
    function _check_install()
    {
        //$this->trace($this->CI, true);
        if (!$this->CI->db->table_exists($this->table_name))
        {
            $sqlstmnt = "
                CREATE TABLE " . $this->table_name . " (
                last_request timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
                tstamp int(16) NOT NULL default '0',
				zipcode int(5) NOT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
            $this->CI->db->query($sqlstmnt);
        }
    }

    /**
    * Request the XML for the weather.com server
    *
    * @access private
    * @param unknown_type $zipcode
    * @return void
    */
    function _requestXML($zipcode)
    {
       	$query = $this->CI->db->query("SELECT tstamp FROM " . $this->table_name . " WHERE zipcode='" . $zipcode . "' LIMIT 0,1");
        if($query->num_rows()==0)
        {
            //$this->seek(true);
			return $this->seek(true,$zipcode);
        }
        else
        {
            $row = $query->row_array();
            if( (time()-$row['tstamp']) > (60*$this->cache_time) )
            {
                // get a fresh copy of the XML;
                //$this->seek(true);
                return $this->seek(true,$zipcode);
            }
            else
            {
                //$this->seek(false);
                return $this->seek(true,$zipcode);
            }
        }
    }

    /**
    * Grab the data....
    *
    * @access private
    * @param bool $fromWeatherCom
    * @return void
    */
    function seek($fromWeatherCom = false,$zipcode)
    {
        $xml_parser = xml_parser_create();

        if($fromWeatherCom==true)
        {
            $data = file_get_contents($this->xmlserver.$zipcode,0);
			if(substr($data,0,6) == '<html>'){
				return 'error';
			}else{
				$data = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $data); 
	
				$data = iconv("GB18030", "utf-8", $data);
				//$data = implode('', file($this->xmlserver.$zipcode));
				write_file($this->wfile, $data);
			}
        }
        else
        {
            $data = read_file($this->wfile);
        }

       $t = true;

		

        $query = $this->CI->db->query("SELECT zipcode FROM " . $this->table_name . " WHERE zipcode='" . $zipcode . "'");
        if( $query->num_rows()==0)
        {
            $this->CI->db->query("INSERT INTO " . $this->table_name . " (zipcode, tstamp) VALUES ('" . $zipcode . "',".time().")");
        }
        else
        {
            $this->CI->db->query("UPDATE " . $this->table_name . " SET tstamp='".time()."' WHERE zipcode='" . $zipcode . "'");
        }
		
		$data_array = new SimpleXMLElement($data);
		return $data_array;
    }

    /**
    * Spit out the data_array
    *
    */
    function trace_array()
    {
        $this->trace($this->data_array, true);
    }
}

?>  