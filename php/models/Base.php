<?php
namespace models;
use PDO;

require_once "../base_include.php";

/* PHANG - Base class
 * Class provides database and caching connections
 *
 *
 *
 */

class Base {

    protected $db;

    public function __construct($dbset = 'default') {
		
		// Database provider setup:
		if (!$settings = parse_ini_file(DB_SETTINGS_DIR, TRUE)) 
		throw new exception('Unable to open ' . DB_SETTINGS_DIR . '.');

		// Arrange database setup variables from settings:
        $dns = $settings[$dbset]['driver'] .
        ':host=' . $settings[$dbset]['host'] .
        ((!empty($settings[$dbset]['port'])) ? (';port=' . $settings[$dbset]['port']) : '') .
        ';dbname=' . $settings[$dbset]['dbname'];
		$username = $settings[$dbset]['username'];
		$password = $settings[$dbset]['password'];

		$this->db = new PDO($dns, $username, $password);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	// Sets database timeout to 5 seconds:
		$this->db->setAttribute(PDO::ATTR_TIMEOUT, 5);


		// Memcache provider setup:
		if (MEMCACHE_LIBRARY == 'memcache') {
			$this->memcache = new Memcache;
		} else if (MEMCACHE_LIBRARY == 'memcached') {
			$this->memcache = new Memcached;
		}
		$this->memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT) or die ("Could not connect to Memcache");		
	}

}
