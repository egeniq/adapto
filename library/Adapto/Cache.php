<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * Base class for all caching systems that atk supports
 * 
 * @package adapto
 * @subpackage cache
 *
 * @copyright (c)2008 Sandy Pleyte
 * @author Sandy Pleyte <sandy@achievo.org>
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */
abstract class Adapto_Cache implements ArrayAccess
{
    /**
     * All cache instances
     * @var array
     */
    private static $m_instances = array();

    /**
     * Is the cache still active.
     * @var bool
     */
    protected $m_active = true;

    /**
     * Lifetime of each cache entry in seconds.
     * @var int
     */
    protected $m_lifetime = 3600;

    /**
     * Namespace so Adapto_Cache can also be used on shared hosts
     * @var string
     */
    protected $m_namespace = "default";

    /**
     * Private Constructor so we can only have 
     * once instance of each cache
     */

    private function __construct()
    {
    }

    /**
     * Get Adapto_Cache instance, default when no type
     * is configured it will use var cache.
     *
     * @param string  $types    Cache type
     * @param boolean $fallback fallback to var cache if all types fail?
     * @param boolean $force    force new instance
     * 
     * @return object Adapto_Cache object of the request type
     */

    public static function getInstance($types = "", $fallback = true, $force = false)
    {
        if ($types == '')
            $types = Adapto_Config::getGlobal("cache_method", array());
        if (!is_array($types))
            $types = array($types);

        foreach ($types as $type) {
            $classname = self::getClassname($type);

            try {
                if (!$force && array_key_exists($type, self::$m_instances) && is_object(self::$m_instances[$type])) {
                    atkdebug("atkcache::getInstance -> Using cached instance of $type cache");
                    return self::$m_instances[$type];
                } else {
                    self::$m_instances[$type] = atknew($classname);
                    self::$m_instances[$type]->setNamespace(Adapto_Config::getGlobal('cache_namespace', 'default'));
                    self::$m_instances[$type]->setLifetime(self::$m_instances[$type]->getCacheConfig('lifetime', 3600));
                    self::$m_instances[$type]->setActive(Adapto_Config::getGlobal('cache_active', true));
                    atkdebug("atkcache::getInstance() -> Using $type cache");

                    return self::$m_instances[$type];
                }
            } catch (Exception $e) {
                atknotice("Can't instantatie Adapto_Cache class $classname: " . $e->getMessage());
            }
        }

        if (!$fallback) {
            throw new Exception("Cannot instantiate Adapto_Cache class of the following type(s): " . implode(', ', $types));
        }

        // Default return var cache
        atkdebug("atkcache::getInstance() -> Using var cache");
        return self::getInstance('var', false, $force);
    }

    /**
     * Get config values from the cache config
     *
     * @param string $key Key
     * @param mixed $default Default value
     * @return mixed
     */

    public function getCacheConfig($key, $default = "")
    {
        $cacheConfig = Adapto_Config::getGlobal('cache', array());
        $type = $this->getType();

        if (array_key_exists($type, $cacheConfig) && array_key_exists($key, $cacheConfig[$type])) {
            return $cacheConfig[$type][$key];
        } else {
            return $default;
        }

    }

    /**
     * Get Classname
     *
     * @param string $type Cache type
     * @return string Classname of the cache type
     */

    private function getClassname($type)
    {
        if (strpos($type, '.') === false) {
            return "atk.cache.atkcache_$type";
        } else {
            return $type;
        }
    }

    /**
     * Turn cache on/off
     *
     * @param boolean $flag Set cache active or not active
     */

    public function setActive($flag)
    {
        $this->m_active = (bool) $flag;
    }

    /**
     * is cache active
     *
     * @return unknown
     */

    public function isActive()
    {
        return $this->m_active;
    }

    /**
     * Set the namespace for the current cache
     *
     * @param string $namespace
     */

    public function setNamespace($namespace)
    {
        $this->m_namespace = $namespace;
    }

    /**
     * Return current namespace that the cache is using
     *
     * @return unknown
     */

    public function getNamespace()
    {
        return $this->m_namespace;
    }

    /**
     * Set the lifetime in seconds for the cache
     *
     * @param int $lifetime Set the lifetime in seconds
     */

    public function setLifetime($lifetime)
    {
        $this->m_lifetime = (int) $lifetime;
    }

    /**
     * Get lifetime of the ache
     *
     * @return int The cache lifetime
     */

    public function getLifetime()
    {
        return $this->m_lifetime;
    }

    /**
     * Add cache entry if it not exists
     * allready
     *
     * @param string $key Entry Id
     * @param mixed $data The data we want to add
     * @param int $lifetime give a specific lifetime for this cache entry. When $lifetime is false the default lifetime is used.
     * @return boolean True on success, false on failure.
     */

    abstract public function add($key, $data, $lifetime = false);

    /**
     * Set cache entry, if it not exists then
     * add it to the cache
     *
     * @param string $key Entry ID
     * @param mixed $data The data we want to set
     * @param int $lifetime give a specific lifetime for this cache entry. When $lifetime is false the default lifetime is used.
     * @return True on success, false on failure.
     */

    abstract public function set($key, $data, $lifetime = false);

    /**
     * get cache entry by key
     *
     * @param string $key Entry id
     * @return mixed Boolean false on failure, cache data on success.
     */

    abstract public function get($key);

    /**
     * delete cache entry
     *
     * @param string $key Entry ID
     * @return void
     */

    abstract public function delete($key);

    /**
     * Deletes all cache entries
     * @return void
     */

    abstract public function deleteAll();

    /**
     * Get realkey for the cache entry
     *
     * @param string $key Entry ID
     * @return string The real entry id
     */

    public function getRealKey($key)
    {
        return $this->m_namespace . "::" . $key;
    }

    /**
     * Get Current cache type
     *
     * @return string Current cache
     */

    public function getType()
    {
        return 'base';
    }

    // ************************
    // * ArrayAcces functions *
    // ************************   

    /**
     * Whether the offset exists
     *
     * @param string $offset Key to check
     * @return boolean
     */
    function offsetExists($offset)
    {
        return ($this->get($offset) !== false);
    }

    /**
     * Value at given offset
     *
     * @param string $offset Key to get
     * @return mixed
     */
    function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set value for given offset
     *
     * @param string $offset Key to set
     * @param mixed $value Value for key
     * @return boolean
     */
    function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * Unset value for given offset
     *
     * @param string $offset Key to unset
     * @return void
     */
    function offsetUnset($offset)
    {
        return $this->delete($offset);
    }
}

?>