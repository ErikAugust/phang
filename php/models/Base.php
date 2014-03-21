<?php
namespace models;
use PDO;

/* PHANG - Base class
 * Class provides database and caching connections
 *
 *
 *
 */

class Base {

    protected $db;
	protected $memcache;
	protected $db_dns;
	protected $db_username;
	protected $db_password;

    public function __construct($dbset = 'default') {

		// Setups
		$this->dbSetup();
		$this->memcacheSetup();
		
		$this->db = new PDO($this->db_dns, $this->db_username, $this->db_password);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	// Sets database timeout to 5 seconds:
		$this->db->setAttribute(PDO::ATTR_TIMEOUT, 5);

	}

	protected function dbSetup() {
		// Database provider setup:
        if (!$settings = parse_ini_file(DB_SETTINGS_DIR, TRUE)) 
        throw new exception('Unable to open ' . DB_SETTINGS_DIR . '.');

        // Arrange database setup variables from settings:
        $this->db_dns = $settings[$dbset]['driver'] .
        ':host=' . $settings[$dbset]['host'] .
        ((!empty($settings[$dbset]['port'])) ? (';port=' . $settings[$dbset]['port']) : '') .
        ';dbname=' . $settings[$dbset]['dbname'];
        $this->db_username = $settings[$dbset]['username'];
        $this->db_password = $settings[$dbset]['password'];
		return true;
	}

	protected function memcacheSetup() {
		// Memcache provider setup:
        if (MEMCACHE_LIBRARY == 'memcache') {
            $this->memcache = new Memcache;
        } else if (MEMCACHE_LIBRARY == 'memcached') {
            $this->memcache = new Memcached;
        }
        $this->memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT) or die ("Could not connect to Memcache");        
	}
}
