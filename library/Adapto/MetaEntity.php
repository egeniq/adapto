<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 *
 * @copyright (c) 2004-2007 Peter C. Verhage
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * The ATK Meta Entity class.
 *
 * Makes it possible to create entitys in 1 line of code
 * using metadata from the database.
 *
 * @author petercv
 *
 * @package adapto
 */
class Adapto_MetaEntity extends Adapto_Entity
{
    /**
     * Meta options.
     *
     * @var array
     */
    private $m_metaOptions;

    /**
     * Constructor.
     *
     * This constructor accepts a variety of parameters in different order.
     * To make this possible (and for supporting passing parameters by reference)
     * the constructor accepts an array which may contain the following fields:
     *
     * - type           entity type
     * - table          table name
     * - sequence       sequence name to use (if not specified, it'll use autoincrement for mysql)
     * - db/database    database name or instance
     * - policy         meta policy, the meta policy to use ((sub-)class atkMetaPolicy instance)
     * - grammar        meta grammar, the meta grammar to use ((sub-)class atkMetaGrammar instance)
     * - compiler       meta compiler, the meta compiler to use ((sub-)class atkMetaCompiler instance)
     * - handler        meta handler, handler which needs to be called instead of the default meta method
     * - flags          entity flags
     * - descriptor     descriptor template for this entity
     * - order          (default) order to sort fields
     * - index          create indexed navigation on a attribute/fieldname
     * - filter         filter
     * - securityAlias  security alias for this entity
     * - securityMap    security map for this entity (will be added to the existing security map!)
     * - cacheable      control whatever this metan entity is cacheable (by default a metaentity is 
     *                  cachable if the meta method is defined static or if there is no meta method 
     *                  defined)
     *
     * All of these variables can also be specified by creating a class variable with
     * the same name. If you do so for flags, and have to use multiple flags, use
     * an array of flags.
     * 
     * @param array $options metan entity options
     * 
     * @return Adapto_MetaEntity
     */

    public function __construct($options = array())
    {
        $this->m_metaOptions = $options;

        $type = $this->getMetaOption('type', strtolower(get_class($this)));

        parent::__construct($type);

        if (!$this->_constructFromCache())
            $this->_constructFromPolicy();

        $this->postMeta();
    }

    /**
     * Old-style constructor for backwards-compatibility.
     *
     * @return Adapto_MetaEntity
     */

    protected function Adapto_MetaEntity()
    {
        $args = func_get_args();
        call_user_func_array(array($this, '__construct'), $args);
    }

    /**
     * Returns the value for the meta option with the given name.
     * 
     * @param string $name     meta option name
     * @param mixed  $default fallback value
     * 
     * @return mixed meta option value
     */

    public function getMetaOption($name, $default = null)
    {
        if (isset($this->$name)) {
            return $this->$name;
        } else if (isset($this->m_metaOptions[$name])) {
            return $this->m_metaOptions[$name];
        } else {
            return $default;
        }
    }

    /**
     * Is the entity structure cacheable?
     * 
     * @return boolean cacheable?
     */

    public function isCacheable()
    {
        $cacheable = $this->getMetaOption('cacheable');
        if ($cacheable !== null)
            return $cacheable;
        if (!Adapto_Config::getGlobal('meta_caching', true))
            return false;
        if (strtolower(get_class($this)) == 'atkmetaentity')
            return false;
        if (!method_exists($this, 'meta'))
            return true;
        $method = new ReflectionMethod(get_class($this), 'meta');
        return $method->isStatic();
    }

    /**
     * Constructs the metan entity from the cache if this entity is cachable and the entity
     * structure has been cached.
     * 
     * @return boolean is the entity constructed from the cache?
     */

    private function _constructFromCache()
    {
        if (!$this->isCacheable())
            return false;

        $file = new Adapto_TmpFile("meta/" . $this->getModule() . "/" . $this->getType() . ".php");
        $module = getModule($this->getModule());
        if ($file->exists()
                && ($module == null || !file_exists($module->getEntityFile(parent::__constructType()))
                        || filemtime($file->getPath()) > filemtime($module->getEntityFile(parent::__constructType())))) {
            $entity = $this;
            include($file->getPath());
            return true;
        }

        return false;
    }

    /**
     * Create policy.
     * 
     * @return atkMetaPolicy policy
     */

    protected function _createPolicy()
    {

        $policy = $this->getMetaOption('policy');
        return atkMetaPolicy::create($this, $policy);
    }

    /**
     * Constructs the metan entity using the meta policy.
     */

    protected function _constructFromPolicy()
    {
        $policy = $this->_createPolicy();
        $policy->apply();
    }

    /**
     * Post meta. 
     * 
     * This method is called just after the entity is constructed from the cache 
     * or using the meta policy and allows you to do some entity initialization
     * which cannot be done by the meta policy.
     */

    public function postMeta()
    {
        // do nothing
    }
}
