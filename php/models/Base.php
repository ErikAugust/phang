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
	public $memcache;
	public $redis;

    public function __construct($dbset = 'default') {

		// Setups
		$this->db = self::dbSetup($dbset);
		$this->memcache = self::memcacheSetup();
		$this->redis = self::redisSetup();
	}

	public static function dbSetup($dbset = 'default') {
		// Database provider setup:
        if (!$settings = parse_ini_file(DB_SETTINGS_DIR, TRUE)) 
        throw new exception('Unable to open ' . DB_SETTINGS_DIR . '.');

        // Arrange database setup variables from settings:
        $db_dns = $settings[$dbset]['driver'] .
        ':host=' . $settings[$dbset]['host'] .
        ((!empty($settings[$dbset]['port'])) ? (';port=' . $settings[$dbset]['port']) : '') .
        ';dbname=' . $settings[$dbset]['dbname'];
        $db_username = $settings[$dbset]['username'];
        $db_password = $settings[$dbset]['password'];
		
		$db = new PDO($db_dns, $db_username, $db_password);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Sets database timeout to 5 seconds:
        $db->setAttribute(PDO::ATTR_TIMEOUT, 5);
		return $db;
	}

	public static function redisSetup() {
        $redis = new \Redis() or die("Cannot load redis module.");
        $redis->connect('127.0.0.1');
        return $redis;
    }

	public static function memcacheSetup() {
		// Memcache provider setup:
        if (MEMCACHE_LIBRARY == 'memcache') {
            $memcache = new \Memcache;
        } else if (MEMCACHE_LIBRARY == 'memcached') {
            $memcache = new \Memcached;
        }
        if (!$memcache->connect(MEMCACHE_HOST, MEMCACHE_PORT)) 
		throw new exception('Unable to connect to Memcache');       
		
		return $memcache;
	}
}
