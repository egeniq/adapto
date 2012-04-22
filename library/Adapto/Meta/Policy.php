<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage meta
 *
 * @copyright (c) 2004-2008 Peter C. Verhage
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *


 */



/**
 * The default meta policy.
 *
 * @author petercv
 *
 * @package adapto
 * @subpackage meta
 */
class Adapto_Meta_Policy
{
  /**
   * No default value.
   */
  const NO_DEFAULT_VALUE = '!@NO_DEFAULT_VALUE@!';

  /**
   * The entity which is being constructed by the policy.
   *
   * @var atkMetaEntity
   */
  protected $m_entity = null;

  /**
   * The meta grammar which is being used by the policy.
   *
   * @var atkMetaGrammar
   */
  protected $m_grammar = null;

  /**
   * The meta compiler which is being used by the policy.
   *
   * @var atkMetaCompiler
   */
  protected $m_compiler = null;

  /**
   * The meta handler which is begin used by the policy. A function name or
   * handler array (class/object and method name). If set to null we will check
   * if the metan entity contains a method called meta and will call this instead.
   *
   * @var mixed
   */
  protected $m_handler = null;

  /**
   * Table meta data.
   *
   * @var array
   */
  protected $m_metaData = null;

  /**
   * Entity database name.
   *
   * @var string
   */
  protected $m_entityDatabase = null;

  /**
   * Entity table name.
   *
   * @var string
   */
  protected $m_entityTable = null;

  /**
   * Entity sequence name.
   *
   * @var string
   */
  protected $m_entitySequence = null;

  /**
   * Entity flags.
   *
   * @var int
   */
  protected $m_entityFlags = null;

  /**
   * Entity descriptor.
   *
   * @var string
   */
  protected $m_entityDescriptor = null;

  /**
   * Entity (default) order.
   *
   * @var string
   */
  protected $m_entityOrder = null;

  /**
   * Entity index column.
   *
   * @var string
   */
  protected $m_entityIndex = null;

  /**
   * Entity filter.
   *
   * @var string
   */
  protected $m_entityFilter = null;

  /**
   * Entity security alias.
   *
   * @var string
   */
  protected $m_entitySecurityAlias = null;

  /**
   * Entity security map.
   *
   * @var string
   */
  protected $m_entitySecurityMap = null;

  /**
   * Included attributes.
   *
   * @var array|null
   */
  protected $m_includes = null;

  /**
   * Excluded attributes.
   *
   * @var array|null
   */
  protected $m_excludes = null;

  /**
   * Entity attributes.
   *
   * @var array
   */
  protected $m_attrs = array();

  /**
   * Returns an instance of the meta policy using the given class. If no class
   * is specified the default meta policy is used determined using the
   * $config_meta_policy variable.
   *
   * @param atkMetaEntity $entity  policy entity
   * @param string      $class full ATK policy class path
   *
   * @return Adapto_Meta_Policy meta policy
   */
  public static function create(atkMetaEntity $entity, $class=null)
  {
    if (!is_string($class) || strlen($class) == 0)
    {
      $class = Adapto_Config::getGlobal("meta_policy", "atk.meta.atkmetapolicy");
    }

    return atknew($class, $entity);
  }

  /**
   * Constructor.
   *
   * @param atkMetaEntity $entity policy entity
   */
  public function __construct(atkMetaEntity $entity)
  {
    $this->setEntity($entity);
  }

  /**
   * Returns the entity for this policy.
   *
   * @return atkEntity policy entity
   */
  public function getEntity()
  {
    return $this->m_entity;
  }

  /**
   * Sets the entity for this policy.
   *
   * @param atkEntity $entity policy entity
   */
  public function setEntity(atkMetaEntity $entity)
  {
    $this->m_entity = $entity;
  }

  /**
   * Returns the meta grammar.
   *
   * @return atkMetaGrammar the meta grammar
   */
  public function getGrammar()
  {
    return $this->m_grammar;
  }

  /**
   * Sets the meta grammar.
   *
   * @param atkMetaGrammar $grammar the meta grammar
   */
  public function setGrammar(atkMetaGrammar $grammar)
  {
    $this->m_grammar = $grammar;
  }

  /**
   * Returns the meta compiler.
   *
   * @return atkMetaCompiler the meta compiler
   */
  public function getCompiler()
  {
    return $this->m_compiler;
  }

  /**
   * Sets the meta compiler.
   *
   * @param atkMetaCompiler $compiler the meta compiler
   */
  public function setCompiler(atkMetaCompiler $compiler)
  {
    $this->m_compiler = $compiler;
  }

  /**
   * Returns the meta handler.
   *
   * @return mixed the meta handler
   */
  public function getHandler()
  {
    return $this->m_handler;
  }

  /**
   * Sets the meta handler.
   *
   * @param mixed $handler the meta compiler
   */
  public function setHandler($handler)
  {
    $this->m_handler = $handler;
  }

  /**
   * Returns the meta data for this policy's table.
   *
   * @return array meta data
   */
  public function getMetaData()
  {
    return $this->m_metaData;
  }

  /**
   * Sets the meta data for this policy's table.
   *
   * @param array $metaData meta data
   */
  protected function _setMetaData($metaData)
  {
    $this->m_metaData = $metaData;
  }

  /**
   * Returns the entity database.
   *
   * @return string database name
   */
  public function getEntityDatabase()
  {
    return $this->m_entityDatabase;
  }

  /**
   * Sets the entity database.
   *
   * Only for internal use, because it's too late to set this from inside the meta method.
   *
   * @param string $database database name
   */
  protected function _setEntityDatabase($database)
  {
    $this->m_entityDatabase = $database;
  }

  /**
   * Returns the entity table.
   *
   * @return string table name
   */
  public function getEntityTable()
  {
    return $this->m_entityTable;
  }

  /**
   * Sets the entity table name.
   *
   * Only for internal use, because it's too late to set this from inside the meta method.
   *
   * @param string $table table name
   */
  protected function _setEntityTable($table)
  {
    $this->m_entityTable = $table;
  }

  /**
   * Returns the entity sequence.
   *
   * @return string sequence name
   */
  public function getEntitySequence()
  {
    return $this->m_entitySequence;
  }

  /**
   * Sets the entity sequence name.
   *
   * @param string $sequence sequence name
   */
  public function setEntitySequence($sequence)
  {
    $this->m_entitySequence = $sequence;
  }

  /**
   * Returns the entity flags.
   *
   * @return int entity flags
   */
  public function getEntityFlags()
  {
    return $this->m_entityFlags;
  }

  /**
   * Sets the entity flags
   *
   * @param int $flags entity flags
   */
  public function setEntityFlags($flags)
  {
    $this->m_entityFlags = $flags;
  }

  /**
   * Returns the entity descriptor.
   *
   * @return string descriptor
   */
  public function getEntityDescriptor()
  {
    return $this->m_entityDescriptor;
  }

  /**
   * Sets the entity descriptor.
   *
   * @param string $descriptor descriptor
   */
  public function setEntityDescriptor($descriptor)
  {
    $this->m_entityDescriptor = $descriptor;
  }

  /**
   * Returns the (default) entity order.
   *
   * @return string entity order
   */
  public function getEntityOrder()
  {
    return $this->m_entityOrder;
  }

  /**
   * Sets the (default) entity order.
   *
   * @param string $order entity order
   */
  public function setEntityOrder($order)
  {
    $this->m_entityOrder = $order;
  }

  /**
   * Returns the index column.
   *
   * @return string index column
   */
  public function getEntityIndex()
  {
    return $this->m_entityIndex;
  }

  /**
   * Sets the entity index column.
   *
   * @param string $column index column
   */
  public function setEntityIndex($column)
  {
    $this->m_entityIndex = $column;
  }

  /**
   * Returns the entity filter.
   *
   * @return string filter
   */
  public function getEntityFilter()
  {
    return $this->m_entityFilter;
  }

  /**
   * Sets the entity filter.
   *
   * @param string $filter filter
   */
  public function setEntityFilter($filter)
  {
    $this->m_entityFilter = $filter;
  }

  /**
   * Returns the entity security alias.
   *
   * @return string security alias
   */
  public function getEntitySecurityAlias()
  {
    return $this->m_entitySecurityAlias;
  }

  /**
   * Sets the entity security alias.
   *
   * @param string $alias security alias
   */
  public function setEntitySecurityAlias($alias)
  {
    $this->m_entitySecurityAlias = $alias;
  }

  /**
   * Returns the additional entity security map
   *
   * @return array security map
   */
  public function getEntitySecurityMap()
  {
    return $this->m_entitySecurityMap;
  }

  /**
   * Sets the entity additional security map.
   *
   * @param array $map security map array
   */
  public function setEntitySecurityMap($map)
  {
    $this->m_entitySecurityMap = $map;
  }

  /**
   * Get attribute type and params for the given attribute/column.
   *
   * Returns an array which looks like the following:
   * array("type" => ..., "params" => array(...))
   *
   * @param string $name attribute/column name
   * @param array  $meta column meta data
   *
   * @return array type and params
   */
  protected function _getTypeAndParams($name, $meta)
  {
    $type = NULL;
    $params = array();

    if (in_array($name, array("passw", "password")))
    {
      $type = "atk.attributes.atkpasswordattribute";
      $params = array(false, 0);
    }
    else if (in_array($name, array("email", "e-mail")))
    {
      $type = "atk.attributes.atkemailattribute";
      $params = array(false);
    }
    else if ($name == 'country')
    {
      $type = 'atk.attributes.atkcountryattribute';
    }
    else if ($name == 'timezone')
    {
      $type = 'atk.attributes.atktimezoneattribute';
    }
    else if ($name == 'created_at' || $name == 'created_on')
    {
      $type = 'atk.attributes.atkcreatestampattribute';
    }
    else if ($name == 'updated_at' || $name == 'updated_on')
    {
      $type = 'atk.attributes.atkupdatestampattribute';
    }
    else if ($name == 'created_by')
    {
      $type = 'atk.attributes.atkcreatedbyattribute';
    }
    else if ($name == 'updated_by')
    {
      $type = 'atk.attributes.atkupdatedbyattribute';
    }
    else if ($meta['gentype'] == 'number' && $meta['len'] == 1 &&
             (substr($name, 0, 3) == 'is_' || substr($name, 0, 4) == 'has_'))
    {
      $type = 'atk.attributes.atkboolattribute';
    }
    else
    {
      switch($meta['gentype'])
      {
        // string
        case "string":
          $type = "atk.attributes.atkattribute";
          break;

        // text
        case "text":
          $type = "atk.attributes.atktextattribute";
          break;

        // number
        case "number":
        case "decimal":
          $type = "atk.attributes.atknumberattribute";
          break;

        // date
        case "date":
          $type = "atk.attributes.atkdateattribute";
          break;

        // time
        case "time":
          $type = "atk.attributes.atktimeattribute";
          break;

        // datetime
        case "datetime":
          $type = "atk.attributes.atkdatetimeattribute";
          break;
      }
    }

    return array("type" => $type, "params" => $params);
  }

  /**
   * Returns the auto-detected flags for the given attribute.
   *
   * @param string $name attribute/column name
   * @param array  $meta column meta data
   *
   * @return int flags
   */
  protected function _getFlags($name, $meta)
  {
    $flags =
      (hasFlag($meta['flags'], MF_PRIMARY) ? AF_PRIMARY : 0) |
      (hasFlag($meta['flags'], MF_UNIQUE) ? AF_UNIQUE : 0) |
      (hasFlag($meta['flags'], MF_NOT_NULL) ? AF_OBLIGATORY : 0) |
      (hasFlag($meta['flags'], MF_AUTO_INCREMENT|MF_PRIMARY) ? AF_AUTOKEY : 0) |
      ($meta['gentype'] == "text" ? AF_HIDE_LIST : 0);

    if (hasFlag($flags, AF_PRIMARY) && $meta['num'] == 0 &&
        in_array($name, array("id", $meta['table']."id", $meta['table']."_id")))
      $flags |= AF_AUTOKEY;

    if (in_array($name, array("passw", "password")))
    {
      $flags |= AF_HIDE_LIST;
    }

    return $flags;
  }

  /**
   * Get auto-detected order for the given attribute.
   *
   * @param string $name column/attribute name
   * @param array  $meta column meta data
   *
   * @return int order
   */
  protected function _getOrder($name, $meta)
  {
    return ($meta['num'] + 1) * 100;
  }


  /**
   * Get auto-detected default value for the given attribute.
   *
   * @param string $name column/attribute name
   * @param array  $meta column meta data
   *
   * @return mixed default value
   */
  protected function _getDefaultValue($name, $meta)
  {
    if (array_key_exists('default', $meta) && $meta['default'] == "NOW" && in_array($meta['gentype'], array('date', 'time', 'datetime')))
    {
      $stamp = getdate();
      $date = array('day' => $stamp['yday'], 'month' => $stamp['mon'], 'year' => $stamp['year']);
      $time = array('hours' => $stamp['hours'], 'minutes' => $stamp['minutes'], 'seconds' => $stamp['seconds']);
      return array_merge($meta['gentype'] == 'time' ? array() : $date, $meta['gentype'] == 'date' ? array() : $time);
    }
    else if (array_key_exists('default', $meta))
    {
      return $meta['default'];
    }
    else
    {
      return self::NO_DEFAULT_VALUE;
    }
  }

  /**
   * Calls a method of this object with the given parameters.
   *
   * @param string $method the method name
   * @param array  $params the method parameters
   *
   * @return mixed result
   */
  protected function _call($method, $params)
  {
    return call_user_func_array(array($this, $method), $params);
  }
  
  /**
   * Import attribute of the given type, we do this as soon as attributes
   * are added to the metan entity or the type has been changed so that the
   * flags for the attribute are available.
   * 
   * @param string $type attribute type
   */
  public function importAttribute($type)
  {
    if (strpos($type, '.') === false && atkexists("attribute", $this->getEntity()->getModule().'.'.$type))
      atkuse("attribute", $this->getEntity()->getModule().'.'.$type);
    elseif (strpos($type, '.') === false && atkexists("relation", $this->getEntity()->getModule().'.'.$type))
      atkuse("relation", $this->getEntity()->getModule().'.'.$type);
    elseif (atkexists("attribute", $type))
      atkuse("attribute", $type);
    elseif (atkexists("relation", $type))
      atkuse("relation", $type);
    else if (!
      throw new Exception("Cannot import attribute of type {$type}");    
  }
  
  /**
   * Returns the attribute modifier for the given attributes. The modifier allows
   * you to add flags, set tabs etc. through a fluent interface.
   *
   * This method can also be called with multiple attribute string arguments.
   * 
   * @param string|array $attrs attribute name(s)
   * 
   * @return atkMetaAttributeModifier
   */
  public function get($attrs)
  {
    if (!is_array($attrs))
      $attrs = func_get_args();
    return atknew('atk.meta.atkmetaattributemodifier', $this, $attrs);
  }
  
  /**
   * Returns an attribute modifier for all attributes.The modifier allows
   * you to add flags, set tabs etc. through a fluent interface.
   * 
   * @return atkMetaAttributeModifier
   */
  public function getAll()
  {
    return $this->get($this->getAttributeNames());
  }

  /**
   * Returns the currently set includes (if applicable).
   *
   * @return array includes list
   */
  public function getIncludes()
  {
    return $this->m_includes;
  }

  /**
   * Set includes. Implicitly sets the order!
   *
   * NOTE: Attributes manually added through the policy will always be
   *       included unless they are explicitly removed using the remove
   *       method!
   *
   * This method can also be called with multiple attribute string arguments.
   *
   * @param string|array $attrs attribute name(s)
   */
  public function setIncludes($attrs)
  {
    if (!is_array($attrs))
      $attrs = func_get_args();

    $this->m_includes = $attrs;
    $this->m_excludes = NULL;

    $this->setOrder($attrs);
  }

  /**
   * Returns the currently set excludes (if applicable).
   *
   * @return array excludes list
   */
  public function getExcludes()
  {
    return $this->m_excludes;
  }

  /**
   * Set excludes.
   *
   * NOTE: Attributes manually added through the policy will always be
   *       included unless they are explicitly removed using the remove
   *       method!
   *
   * This method can also be called with multiple attribute string arguments.
   *
   * @param string|array $attrs attribute name(s)
   */
  public function setExcludes($attrs)
  {
    if (!is_array($attrs))
      $attrs = func_get_args();

    $this->m_excludes = $attrs;
    $this->m_includes = NULL;
  }

  /**
   * Compares two attributes based on their order.
   * 
   * @param array $a attribute
   * @param array $b attribute
   *
   * @return int order (a == b ? 0 : (a < b ? -1 : 1))
   */
  public static function cmpAttributes($a, $b)
  {
    if (!isset($a['order'], $b['order']) || $a['order'] == $b['order'])
    {
      return 0;
    }
    else if ($a['order'] < $b['order'])
  	{
  	  return -1;   
  	}
  	else
  	{
  	  return 1;  
  	} 
  }
  
  /**
   * Sort the attributes based on their order.
   */
  protected function sortAttributes()
  {
  	uasort($this->m_attrs, array('Adapto_Meta_Policy', 'cmpAttributes'));
  }

  /**
   * Sets the attribute order. All attributes not mentioned will be moved to
   * the bottom using their current order.
   *
   * This method can also be called with multiple attribute string arguments.
   *
   * @param string|array $attrs attribute name(s)
   */
  public function setOrder($attrs)
  {
    if (func_num_args() > 1)
      $attrs = func_get_args();
    else if (!is_array($attrs))
      $attrs = array($attrs);

    $order = array_merge($attrs, array_diff(array_keys($this->m_attrs), $attrs));

    foreach ($order as $i => $key)
      $this->m_attrs[$key]['order'] = ($i + 1) * 100;
      
    $this->sortAttributes();      
  }

  /**
   * Set the default value for the given attribute(s).
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the default value.
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param mixed $value         default value
   */
  public function setDefaultValue($attrs, $value)
  {
    if (func_num_args() > 2)
    {
      $attrs = func_get_args();
      $tabs = array_pop($attrs);
    }

    else if (!is_array($attrs))
      $attrs = array($attrs);

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["default"] = $value;
  }

  /**
   * Set flag(s) for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the flag(s).
   *
   * NOTE: this method will overwrite all currently set flags, including
   *       automatically detected flags!
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param int          $flags  flag(s)
   */
  public function setFlag($attrs, $flags)
  {
    $params = func_get_args();
    $this->_call("setFlags", $params);
  }

  /**
   * Set flag(s) for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the flag(s).
   *
   * NOTE: this method will overwrite all currently set flags, including
   *       automatically detected flags!
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param int          $flags  flag(s)
   */
  public function setFlags($attrs, $flags)
  {
    if (func_num_args() > 2)
    {
      $attrs = func_get_args();
      $flags = array_pop($attrs);
    }

    else if (!is_array($attrs))
      $attrs = array($attrs);

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["flags"] = $flags;
  }

  /**
   * Add flag(s) for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the flag(s).
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param int          $flags  flag(s)
   */
  public function addFlag($attrs, $flags)
  {
    $params = func_get_args();
    $this->_call("addFlags", $params);
  }

  /**
   * Add flag(s) for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the flag(s).
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param int          $flags  flag(s)
   */
  public function addFlags($attrs, $flags)
  {
    if (func_num_args() > 2)
    {
      $attrs = func_get_args();
      $flags = array_pop($attrs);
    }
    else if (!is_array($attrs))
      $attrs = array($attrs);

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["flags"] |= $flags;
  }

  /**
   * Add default flag(s) to all attributes, except the specifed attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the flag(s).
   *
   * @param array|string $exclude_attrs  list of attribute names or single attribute name to exclude
   * @param int          $flags          flag(s)
   */
  public function addDefaultFlags($exclude_attrs, $flags=0)
  {
    if ((func_num_args() == 1) || (func_num_args() > 2))
    {
      $exclude_attrs = func_get_args();
      $flags = array_pop($exclude_attrs);
    }
    else if (!is_array($exclude_attrs))
      $exclude_attrs = array($exclude_attrs);
      
    foreach (array_keys($this->m_attrs) as $key)
    {
      if (!in_array($key, $exclude_attrs))
      {
        $this->m_attrs[$key]["flags"] |= $flags;
      }
    }
  }

  /**
   * Remove flag(s) from the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the flag(s).
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param int          $flags  flag(s)
   */
  public function removeFlag($attrs, $flags)
  {
    $params = func_get_args();
    $this->_call("removeFlags", $params);
  }

  /**
   * Remove flag(s) from the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the flag(s).
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param int          $flags  flag(s)
   */
  public function removeFlags($attrs, $flags)
  {
    if (func_num_args() > 2)
    {
      $attrs = func_get_args();
      $flags = array_pop($attrs);
    }

    else if (!is_array($attrs))
      $attrs = array($attrs);

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["flags"] = ($this->m_attrs[$attr]["flags"] | $flags) ^ $flags;
  }

  /**
   * Enable force insert for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments.
   *
   * @param string|array $attrs attribute name(s)
   */
  public function addForceInsert($attrs)
  {
    if (!is_array($attrs))
      $attrs = func_get_args();

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["forceInsert"] = true;
  }

  /**
   * Disable force insert for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments.
   *
   * @param string|array $attrs attribute name(s)
   */
  public function removeForceInsert($attrs)
  {
    if (!is_array($attrs))
      $attrs = func_get_args();

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["forceInsert"] = false;
  }

  /**
   * Enable force update for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments.
   *
   * @param string|array $attrs attribute name(s)
   */
  public function addForceUpdate($attrs)
  {
    if (!is_array($attrs))
      $attrs = func_get_args();

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["forceUpdate"] = true;
  }

  /**
   * Disable force update for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments.
   *
   * @param string|array $attrs attribute name(s)
   */
  public function removeForceUpdate($attrs)
  {
    if (!is_array($attrs))
      $attrs = func_get_args();

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["forceUpdate"] = false;
  }

  /**
   * Set the sections/tabs for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the section name(s).
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param array|string $tabs   tab name(s)
   */
  function setTab($attrs, $tabs)
  {
    $params = func_get_args();
    $this->_call("setTabs", $params);
  }

  /**
   * Set the sections/tabs for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the section name(s).
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param array|string $tabs   tab name(s)
   */
  public function setTabs($attrs, $tabs)
  {
    if (func_num_args() > 2)
    {
      $attrs = func_get_args();
      $tabs = array_pop($attrs);
    }

    else if (!is_array($attrs))
      $attrs = array($attrs);

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["tabs"] = $tabs;
  }

  /**
   * Set the sections/tabs for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the section name(s).
   *
   * @param array|string $attrs    list of attribute names or single attribute name
   * @param array|string $sections section name(s)
   */
  public function setSection($attrs, $sections)
  {
    $params = func_get_args();
    $this->_call("setTabs", $params);
  }

  /**
   * Set the sections/tabs for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the section name(s).
   *
   * @param array|string $attrs    list of attribute names or single attribute name
   * @param array|string $sections section name(s)
   */
  public function setSections($attrs, $sections)
  {
    $params = func_get_args();
    $this->_call("setTabs", $params);
  }
  
  /**
   * Set the column for the given attribute or list of attributes.
   *
   * This method can also be called with multiple attribute string arguments
   * as long as the last argument contains the section name(s).
   *
   * @param array|string $attrs  list of attribute names or single attribute name
   * @param array|string $column colum name
   */
  public function setColumn($attrs, $column)
  {
    if (func_num_args() > 2)
    {
      $attrs = func_get_args();
      $column = array_pop($attrs);
    }

    else if (!is_array($attrs))
      $attrs = array($attrs);

    foreach ($attrs as $attr)
      if (array_key_exists($attr, $this->m_attrs))
        $this->m_attrs[$attr]["column"] = $column;
  }
  
  /**
   * Sets the attribute type. All extra arguments after the two
   * standard arguments will be threated as parameters for the
   * attribute. If you need to pass arguments by reference you can
   * better use the setTypeAndParams method.
   *
   * @param string|array $attr  the attribute name or a list of attributes
   * @param string $type        full ATK attribute class (e.g. atk.attributes.atkboolattribute)
   * @param mixed ...           all other arguments will be threated as parameters
   */
  public function setType($attr, $type)
  {
    $params = func_get_args();
    $params = array_slice($params, 2);
    $this->setTypeAndParams($attr, $type, $params);
  }

  /**
   * Sets the attribute type and parameters.
   *
   * @param string|array $attr   the attribute name or a list of attributes
   * @param string       $type   full ATK attribute class (e.g. atk.attributes.atkboolattribute)
   * @param array        $params parameters for the attribute (optional)
   */
  public function setTypeAndParams($attr, $type, $params=array())
  {
    $this->importAttribute($type);
    
    $attrs = is_array($attr) ? $attr : array($attr);
    foreach ($attrs as $attr)
    {
      $this->m_attrs[$attr]["type"] = $type;
      $this->m_attrs[$attr]["params"] = $params;
    }
  }

  /**
   * Returns the current maximum order in the attribute list.
   *
   * @return int max order
   */
  protected function _getMaxOrder()
  {
    $max = 0;

    foreach (array_keys($this->m_attrs) as $key)
    {
      if (isset($this->m_attrs[$key]["order"]) && $this->m_attrs[$key]["order"] > $max)
      {
        $max = $this->m_attrs[$key]["order"];
      }
    }

    return $max;
  }

 /**
   * Find destination entity for the given meta relation.
   *
   * @param string $accessor  accessor name
   * @param bool   $toMany    accessor name might be in plural form?
   *
   * @return string destination entity name for the given relation
   */
  protected function _findDestination($accessor, $toMany)
  {
    $module = getEntityModule($accessor);
    if ($module == "")
      $module = $this->m_entity->m_module;

    $entity = getEntityType($accessor);

    if ($module != "")
    {
      if (entityExists("$module.$entity"))
        return "$module.$entity";

      if ($toMany && entityExists("$module.".$this->getGrammar()->singularize($entity)))
        return "$module.".$this->getGrammar()->singularize($entity);

      if (!$toMany && entityExists("$module.".$this->getGrammar()->pluralize($entity)))
        return "$module.".$this->getGrammar()->pluralize($entity);

      if (entityExists("{$module}.{$module}_{$entity}"))
        return "{$module}.{$module}_{$entity}";

      if ($toMany && entityExists("{$module}.{$module}_".$this->getGrammar()->singularize($entity)))
        return "{$module}.{$module}_".$this->getGrammar()->singularize($entity);

      if (!$toMany && entityExists("{$module}.{$module}_".$this->getGrammar()->pluralize($entity)))
        return "{$module}.{$module}_".$this->getGrammar()->pluralize($entity);
    }

    if (entityExists($entity))
      return $entity;

    if ($toMany && entityExists($this->getGrammar()->singularize($entity)))
      return $this->getGrammar()->singularize($entity);

    if (!$toMany && entityExists($this->getGrammar()->pluralize($entity)))
      return $this->getGrammar()->pluralize($entity);

    return NULL;
  }

  /**
   * Returns a list of possible attribute name variants for relations
   * which reference this entity or the given destination entity.
   *
   * @param string $destination destination entity
   *
   * @return array list of attribute variants
   */
  protected function _getDestinationAttributeVariants($destination=null)
  {
    $base = array();

    // no destination given, self is assumed, we also add the table name
    // and parent classes as base for the different variants
    if ($destination == null)
    {
      $module = getEntityModule($this->getEntity()->atkEntityType());

      $base[] = $this->getEntityTable();
      $base[] = getEntityType($this->getEntity()->atkEntityType());

      for ($class = get_class($this->getEntity()); stripos($class, 'metaentity') === false; $class = get_parent_class($class))
      {
        $base[] = strtolower($class);
      }

      $base = array_unique($base);
    }
    else
    {
      $module = getEntityModule($destination);
      $base[] = getEntityType($destination);
    }

    if ($module != null)
    {
      // add variants for each base with the module as prefix or with the module
      // prefix stripped out (if it was already part of base), we explicitly
      // make a copy of base so that new entries don't mess up the loop
      foreach (array_values($base) as $entry)
      {
        // entry already contains module prefix, strip it
        if (substr($entry, 0, strlen($module) + 1) == $module.'_')
        {
          $base[] = substr($entry, strlen($module) + 1);
        }

        // entry doesn't contain prefix yet, add it
        else
        {
          $base[] = $module."_".$entry;
        }
      }
    }

    $variants = array();

    foreach ($base as $entry)
    {
      $variants[] = "{$entry}_id";
      $variants[] = $this->getGrammar()->singularize($entry)."_id";
      $variants[] = "{$entry}id";
      $variants[] = $this->getGrammar()->singularize($entry)."id";
      $variants[] = $entry;
      $variants[] = $this->getGrammar()->singularize($entry);
    }

    $variants = array_values(array_unique($variants));

    return $variants;
  }

  /**
   * Find source attribute for a many-to-one relation that point to the
   * given destination entity.
   *
   * @param string $destination destination entity type
   *
   * @return string source attribute name
   */
  protected function _findSourceAttribute($destination)
  {
    $module = getEntityModule($destination);
    $type = getEntityType($destination);

    $prefixes = $module == null ? array('') : array('', "{$module}_");

    foreach ($prefixes as $leftPrefix)
    {
      foreach ($prefixes as $rightPrefix)
      {
        foreach (array_keys($this->m_attrs) as $name)
        {
          switch ($leftPrefix.$name)
          {
            case "{$rightPrefix}{$type}_id":
            case "{$rightPrefix}{$type}id":
            case $rightPrefix.$this->getGrammar()->singularize($type)."_id":
            case $rightPrefix.$this->getGrammar()->singularize($type)."id":
            case $rightPrefix.$type:
            case $rightPrefix.$this->getGrammar()->singularize($type):
              return $name;
          }
        }
      }
    }

    return null;
  }

  /**
   * One-to-many / many-to-many relation support. You can call the hasMany
   * method to indicate that this entity has a one-to-many or a many-to-many
   * relationship with another entity. The meta policy will then try to guess,
   * amongst other things, which fields should be used for this relation.
   *
   * This method uses a smart name guessing scheme for the (optional
   * intermediate) and destination entity. If you enter the plural form of
   * the (singular) entity name it will still be able to find the entity.
   * You can ommit the module name prefix if the destination entity resides
   * in the same module as the source entity. Ofcourse you can also just use
   * the real module/entity name combination.
   *
   * The options list may contain several parameters to make more complex
   * relations work. The supported parameters are as follows:
   *
   * - dest(-ination)  destination attribute name
   * - filter          destination filter
   * - through         intermediary entity name (for many-to-many relations)
   * - local           if ATK can't determine the key in the intermediary entity
   *                   automatically, use local to tell it which key points to
   *                   the source entity.
   * - remote          if ATK can't determine the key in the intermediary entity
   *                   use remote to tell it which key points to the 
   *                   destination entity. 
   * - type            type of many-to-many relation (shuttle, select, 
   *                   eshuttle, bool(ean) or list, defaults to shuttle)
   * - cols/columns    number of columns (many-to-many bool relations only)
   * - rows            number of rows (many-to-many list relations only)
   * - name            name for this relation (by default getEntityType($accessor))
   *
   * @param string       $accessor          accessor name (complete description is given above)
   * @param string|array $templateOrOptions template or list of options (complete description is given above)
   * @param array        $options           list op options (complete description is given above)
   * @param int          $flags 	          the flags for the relation
   * 
   * @return atkMetaAttributeModifier
   */
  public function hasMany($accessor, $templateOrOptions=array(), $options=array(), $flags=0)
  {
    $template = NULL;
    if (is_array($templateOrOptions))
      $options = $templateOrOptions;
    else $template = $templateOrOptions;

    if (isset($options['name']))
    {
      $name = $options['name'];
    }
    else
    {
      $name = getEntityType($accessor);
    }

    if (isset($options['source']))
    {
      $options['local'] = $options['source'];
    }
    
    if (isset($options['class']))
    {
      $type = $options['class'];
    }

    $destination = $this->_findDestination($accessor, true);
    if (empty($destination))
    {
      throw new Exception("Cannot find destination for ".$this->getEntity()->atkEntityType()."::hasMany({$accessor}, ...)");
    }

    if (isset($options['through']))
    {
      if (!isset($type))
      {
        switch (@$options['type'])
        {
          case 'bool':
          case 'boolean':
            $type = "atk.meta.relations.atkmetamanyboolrelation";
            break;
          case 'list':
            $type = "atk.meta.relations.atkmetamanytomanylistrelation";
            break;
          case 'select':
            $type = "atk.meta.relations.atkmetamanytomanyselectrelation";
            break;
          case 'eshuttle':
          case 'extendableshuttle':
            $type = "atk.meta.relations.atkmetaextendableshuttlerelation";
            break;
          case 'shuttle':
          default:
            $type = "atk.meta.relations.atkmetashuttlerelation";
        }
      }

      $through = $this->_findDestination($options['through'], true);
      if (empty($through))
      {
        throw new Exception("Cannot find intermediate entity for ".$this->getEntity()->atkEntityType()."::hasMany({$accessor}, array(through => {$options['through']}, ...))");
      }      

      if (!isset($options['local']))
      {
        $options['localVariants'] = $this->_getDestinationAttributeVariants();
      }

      if (!isset($options['remote']))
      {
        $remoteVariants = $this->_getDestinationAttributeVariants($destination);
        if (isset($options['name']))
          $remoteVariants = array_merge($remoteVariants, $this->_getDestinationAttributeVariants($options['name']));
        $options['remoteVariants'] = $remoteVariants;
      }

      $params = array($destination, $through, $template, $options);
    }
    else
    {
      if (!isset($type))
      {
        $type = "atk.meta.relations.atkmetaonetomanyrelation";
      }
      
      $variants = $this->_getDestinationAttributeVariants();
      $options['variants'] = $variants;

      $params = array($destination, $template, $options);
    }

    $flags  = AF_HIDE_LIST|AF_HIDE_ADD|$flags;
    $tabs   = NULL;
    $order  = $this->_getMaxOrder() + 100 ;

    return $this->add($name, $type, $params, $flags, $tabs, $order);
  }

  /**
   * Many-to-one / one-to-one relation support. You can call the hasOne method
   * to indicate that this entity has a many-to-one or a one-to-one relation with
   * another entity. The meta policy will then try to guess, amongst other
   * things, which fields should be used for this relation.
   *
   * To determine if a many-to-one or a one-to-one relation should be used
   * the system will check if the source entity contains an attribute for
   * storing the relation. If so the system will use a many-to-one relation,
   * else a one-to-one relation will be used.
   *
   * This method uses a smart name guessing scheme for the destination entity.
   * If you enter the singular form of the (plural) entity name it will still
   * be able to find the entity. You can ommit the module name prefix if the
   * destination entity resides in the same module as the source entity. Ofcourse
   * you can also just use the real module/entity name combination.
   *
   * The options list may contain several parameters to make more complex
   * relations work. The supported parameters are as follows:
   *
   * - source          source attribute name (should only be used for
   *                   many-to-one relations and will act as an indicator
   *                   for whatever this is a many-to-one relation or not)
   * - dest(-ination)  destination attribute name  (should only be used for
   *                   one-to-one relations and will act as an indicator
   *                   for whatever this is a one-to-one relation or not)
   * - filter          destination filter
   * - large           boolean indicating if there will be lots and lots of
   *                   records in case of a many-to-one relation, same as
   *                   the AF_LARGE flag (defaults to false)
   *
   * @param string       $accessor          accessor name (complete description is given above)
   * @param string|array $templateOrOptions template or list of options (complete description is given above)
   * @param array        $options           list op options (complete description is given above)
   * @param int          $flags 	          the flags for the relation
   * 
   * @return atkMetaAttributeModifier
   */
  public function hasOne($accessor, $templateOrOptions=array(), $options=array(), $flags=0)
  {
    $template = NULL;
    if (is_array($templateOrOptions))
      $options = $templateOrOptions;
    else $template = $templateOrOptions;

    // look-up destination entity
    $destination = $this->_findDestination($accessor, false);
    if (empty($destination))
    {
      throw new Exception("Cannot find destination for ".$this->getEntity()->atkEntityType()."::hasOne($accessor, ...)");
    }

    // explicit source given
    if (array_key_exists("source", $options))
    {
      // in case of multi referential key "source" is array
      if (is_array($options["source"]))
      {
        $attr = $options["source"][0]; // we use the first key as name of attribute
      }
      else
      {
        $attr = $options["source"];
      }
    }

    // no source and no destination given, still try to find a source attribute just to be sure
    // note that findSourceAttribute probably returns null for one-to-one relations
    else if (!array_key_exists("dest", $options) && !array_key_exists("destination", $options))
    {
      $attr = $this->_findSourceAttribute($destination);
    }

    // one-to-one relation, lookup possible destination attribute variants
    if ($attr == null && !array_key_exists("dest", $options) && !array_key_exists("destination", $options))
    {
      $options['variants'] = $this->_getDestinationAttributeVariants();
    }

    $name   = $attr != NULL ? $attr : getEntityType($accessor);
    $type   = "atk.meta.relations.atkmeta".($attr != NULL ? 'many' : 'one')."toonerelation";
    $params = array($destination, $template, $options);
    $flags  = ($attr != NULL ? $this->m_attrs[$attr]["flags"] : 0) | (array_key_exists("large", $options) && $options["large"] ? AF_LARGE : 0) | $flags;
    $tabs   = $attr != NULL ? $this->m_attrs[$attr]["tabs"] : NULL;
    $order  = $attr != NULL ? $this->m_attrs[$attr]["order"] : $this->_getMaxOrder() + 100;

    return $this->add($name, $type, $params, $flags, $tabs, $order);
  }

  /**
   * Add / replace (custom) attribute.
   *
   * @param string|array $name     attribute name or list of attributes
   * @param string       $type     attribute type
   * @param array        $params   attribute parameters, excluding flags (optional)
   * @param int          $flags    attribute flags (optional)
   * @param string|array $sections sections/tabs to display the attribute on
   * @param int          $order    order of the attribute
   * @param mixed        $default  default value
   * 
   * @return atkMetaAttributeModifier
   */
  public function add($name, $type='atkattribute', $params=array(), $flags=0, $sections=NULL, $order=NULL, $default=self::NO_DEFAULT_VALUE)
  {
    $this->importAttribute($type);
    
    $names = is_array($name) ? $name : array($name);
    foreach ($names as $name)
    {
      if ($order === NULL && isset($this->m_attrs[$name]))
      {
        $order = $this->m_attrs[$name]['order'];
      }
      else if ($order === NULL)
      {
        $order = $this->_getMaxOrder() + 100;
      }

      $this->m_attrs[$name] =
        array(
          "type" => $type,
          "params" => $params,
          "flags" => $flags,
          "tabs" => $sections,
          "column" => null,
          "order" => $order
        );

      if ($default != self::NO_DEFAULT_VALUE)
      {
        $this->m_attrs[$name]['default'] = $default;
      }
    }
    
    $this->sortAttributes();
    
    return atknew('atk.meta.atkmetaattributemodifier', $this, $names);
  }
  
  /**
   * Add fieldset.
   * 
   * To include an attribute label use [attribute.label] inside your
   * template. To include an attribute edit/display field use 
   * [attribute.field] inside your template.
   *
   * @param string       $name     name
   * @param string       $template template string
   * @param int          $flags    attribute flags
   * @param string|array $sections sections/tabs to display the attribute on
   * @param int          $order    order of the attribute
   * 
   * @return atkMetaAttributeModifier
   */
  public function addFieldSet($name, $template, $flags=0, $sections=NULL, $order=NULL)
  {
    return $this->add($name, 'atk.attributes.atkfieldset', array($template), $flags, $sections, $order);
  }  

  /**
   * Remove attribute.
   *
   * @param string|array $name attribute name
   */
  public function remove($name)
  {
    $names = is_array($name) ? $name : func_get_args();
    
    foreach ($names as $name)
    {
      unset($this->m_attrs[$name]);
    }
  }
  
  /**
   * Does the given attribute exist?
   *
   * @param string $name attribute name
   */
  public function exists($name)
  {
    return isset($this->m_attrs[$name]);
  }

  /**
   * Returns a reference to the attributes array.
   *
   * Be very careful when using this array, modifying it might void your warranty!
   *
   * @return array reference to the attributes array
   */
  public function &getAttributes()
  {
    return $this->m_attrs;
  }

  /**
   * Returns the attribute names.
   *
   * @return array string attribute names
   */
  public function getAttributeNames()
  {
    return array_keys($this->m_attrs);
  }

  /**
   * Translate using the entity's module and type.
   *
   * @param mixed $string           string or array of strings containing the name(s) of the string to return
   *                                when an array of strings is passed, the second will be the fallback if
   *                                the first one isn't found, and so forth
   * @param String $module          module in which the language file should be looked for,
   *                                defaults to core module with fallback to ATK
   * @param String $language        ISO 639-1 language code, defaults to config variable
   * @param String $firstFallback   the first module to check as part of the fallback
   * @param boolean $entityDefaultText  if true, then it doesn't return a default text
   *                                when it can't find a translation
   * @return String the string from the languagefile
   */
  public function text($string, $module=NULL, $language='', $firstFallback="", $entityDefaultText=false)
  {
    return $this->getEntity()->text($string, $module, $language, $firstFallback, $entityDefaultText);
  }

  /**
   * Utility method to bit-or two integers.
   *
   * @param int $a integer a
   * @param int $b integer b
   *
   * @return int result of bit-or
   */
  public static function bitOr($a, $b)
  {
    return $a|$b;
  }

  /**
   * Detect entity table name.
   *
   * @return string table name
   */
  protected function _detectEntityTable()
  {
    $module = $this->getEntity()->getModule();

    $base = array();
    $base[] = $this->getEntity()->getType();
    $base[] = $module."_".$this->getEntity()->getType();

    for ($class = get_class($this->getEntity()); stripos($class, 'metaentity') === false; $class = get_parent_class($class))
    {
      $base[] = strtolower($class);
      $base[] = $module."_".strtolower($class);
    }

    $db = atkGetDb($this->getEntityDatabase());
    foreach ($base as $entry)
    {
      if ($db->tableExists($entry))
      {
        return $entry;
      }
      else if ($db->tableExists($this->getGrammar()->singularize($entry)))
      {
        return $this->getGrammar()->singularize($entry);
      }
      else if ($db->tableExists($this->getGrammar()->pluralize($entry)))
      {
        return $this->getGrammar()->pluralize($entry);
      }
    }

    return null;
  }

  /**
   * Detect entity sequence name.
   *
   * @return string sequence name
   */
  protected function _detectEntitySequence()
  {
    $cols = $this->getMetaData();
    $sequence = NULL;

    foreach ($cols as $meta)
    {
      if (isset($meta['sequence']) && strlen($meta['sequence']) > 0)
      {
        $sequence = $meta['sequence'];
      }
    }

    if ($sequence == NULL)
    {
      $sequence = Adapto_Config::getGlobal("database_sequenceprefix").$this->getEntityTable();
    }

    return $sequence;
  }

  /**
   * Intialize attribute for entity using the given column meta data.
   *
   * @param string $name column name
   * @param array  $meta column meta data
   */
  protected function _initAttribute($name, $meta)
  {
    $typeAndParams = $this->_getTypeAndParams($name, $meta);
    if ($typeAndParams["type"] === NULL) return;

    $type = $typeAndParams['type'];
    $params = $typeAndParams['params'];
    $flags = $this->_getFlags($name, $meta);
    $order = $this->_getOrder($name, $meta);
    $default = $this->_getDefaultValue($name, $meta);

    $this->add($name, $type, $params, $flags, null, $order, $default);
  }

  /**
   * Initialize attributes using policy.
   */
  protected function _init()
  {
    $grammar = $this->getEntity()->getMetaOption('grammar');
    $grammar = atkMetaGrammar::get($grammar);
    $this->setGrammar($grammar);

    $compiler = $this->getEntity()->getMetaOption('compiler');
    $compiler = atkMetaCompiler::get($compiler);
    $this->setCompiler($compiler);

    $handler = $this->getEntity()->getMetaOption('handler');
    $this->setHandler($handler);

    $database = $this->getEntity()->getMetaOption('database', $this->getEntity()->getMetaOption('db', 'default'));
    $this->_setEntityDatabase($database);

    $table = $this->getEntity()->getMetaOption('table');
    if ($table == null)
      $table = $this->_detectEntityTable();
    $this->_setEntityTable($table);

    $db = atkGetDb($database);
    if ($table == null)
    {
      throw new Exception("No table found for metan entity " . $this->getEntity()->atkEntityType() . "! Are you sure you are connecting to the right database?");
    }
    else if (!$db->tableExists($table))
    {
      throw new Exception("Table {$table}, referenced by metan entity " . $this->getEntity()->atkEntityType() . ", does not exist! Are you sure you are connecting to the right database?");
    }

    $metaData = $db->tableMeta($table);
    $this->_setMetaData($metaData);

    $sequence = $this->getEntity()->getMetaOption('sequence');
    if ($sequence == null)
      $sequence = $this->_detectEntitySequence();
    $this->setEntitySequence($sequence);

    $flags = $this->getEntity()->getMetaOption('flags', 0);
    if (is_array($flags))
      $flags = array_reduce($flags, array('Adapto_Meta_Policy', 'bitOr'), 0);
    $this->setEntityFlags($flags);

    $descriptor = $this->getEntity()->getMetaOption('descriptor');
    $this->setEntityDescriptor($descriptor);

    $order = $this->getEntity()->getMetaOption('order');
    $this->setEntityOrder($order);

    $index = $this->getEntity()->getMetaOption('index');
    $this->setEntityIndex($index);

    $filter = $this->getEntity()->getMetaOption('filter');
    $this->setEntityFilter($filter);

    $securityAlias = $this->getEntity()->getMetaOption('securityAlias');
    $this->setEntitySecurityAlias($securityAlias);

    $securityMap = $this->getEntity()->getMetaOption('securityMap');
    $this->setEntitySecurityMap($securityMap);

    foreach ($metaData as $name => $meta)
      $this->_initAttribute($name, $meta);
  }

  /**
   * Modify meta policy, by default the meta method of the entity is called.
   */
  protected function _meta()
  {
    // handler / callback is set, call the handler
    if ($this->getHandler() != null)
    {
      call_user_func($this->getHandler(), $this);
      return;
    }

    // no handler set, try to call the entity's meta method if it exists
    if (!method_exists($this->getEntity(), 'meta')) return;

    $method = new ReflectionMethod($this->getEntity(), 'meta');

    if ($method->isStatic())
    {
      $method->invoke(get_class($this->getEntity()), $this);
    }
    else
    {
      $this->getEntity()->meta($this);
    }
  }

  /**
   * Compile policy.
   *
   * @return string code compiled code
   */
  protected function _compile()
  {
    return $this->getCompiler()->compile($this);
  }

  /**
   * Write compiled metan entity code to cache.
   *
   * @param string $code compiled code
   *
   * @return string file path
   */
  protected function _cache($code)
  {
    
    $file = new Adapto_TmpFile("meta/".$this->getEntity()->getModule()."/".$this->getEntity()->getType().".php");
    $file->writeFile("<?php\n$code");
    return $file->getPath();
  }

  /**
   * Build / setup entity using the collected attributes.
   */
  public function apply()
  {
    $this->_init();
    $this->_meta();
    $code = $this->_compile();

    if (Adapto_Config::getGlobal('debug') > 2)
    {
      Adapto_var_dump("\n\n$code", "Adapto_Meta_Policy::apply - ".$this->getEntity()->atkEntityType());
    }

    // needed for included and eval'ed code!
    $entity = $this->getEntity();
    
    if ($this->getEntity()->isCacheable())
    {
      $file = $this->_cache($code);
      include($file);
    }
    else
    {
      eval($code);
    }
  }
}