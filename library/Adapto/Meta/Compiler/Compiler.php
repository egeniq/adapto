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
 * @copyright (c) 2004-2005 Peter C. Verhage
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * The meta policy compiler, transforms a meta policy to entity code.
 * 
 * @author petercv
 *
 * @package adapto
 * @subpackage meta
 */
class Adapto_Meta_Compiler
{
    /**
     * Compiler instances.
     *
     * @var array instances
     */
    static $instances;

    /**
     * Returns an instance of the meta compiler with the given class. If no class
     * is specified the default meta compiler is used determined using the 
     * $config_meta_compiler variable.
     *
     * @param string $class full ATK compiler class path
     * 
     * @return Adapto_Meta_Compiler meta compiler
     */

    public static function get($class = null)
    {
        if (!is_string($class) || strlen($class) == 0) {
            $class = Adapto_Config::getGlobal("meta_compiler", "atk.meta.compiler.atkmetacompiler");
        }

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = atknew($class);
        }

        return self::$instances[$class];
    }

    /**
     * Generates an flags string for a given flags value.
     * 
     * @param int    $flags            flags
     * @param string $prefix           flags prefix
     * @param array  $specificExcludes values of customizable flags which can differ for subclasses
     * @param array  $constExcludes    constant names of constants that should be excluded
     * 
     * @return string flags string
     */

    protected function _getFlagsString($flags, $prefix, $specificExcludes = array(), $constExcludes = array())
    {
        static $constants = array();

        if (!isset($constants[$prefix])) {
            $allConstants = get_defined_constants();

            $constants[$prefix] = array();
            foreach ($allConstants as $const => $value) {
                if (substr($const, 0, strlen($prefix)) == $prefix
                        && (substr($const, 0, strlen($prefix) + 9) == $prefix . 'SPECIFIC_' || !in_array($value, $specificExcludes))
                        && !in_array($const, $constExcludes)) {
                    $constants[$prefix][$value] = $const;
                }
            }

            krsort($constants[$prefix]);
        }

        $found = array();
        foreach ($constants[$prefix] as $value => $const) {
            if (hasFlag($flags, $value)) {
                $found[] = $const;
                $flags ^= $value;
            }
        }

        return count($found) > 0 ? implode('|', $found) : 0;
    }

    /**
     * Generates an attributes flags string for a given flags value.
     * 
     * @param int $flags flags
     * 
     * @return string flags string
     */

    protected function _getAttributeFlagsString($flags)
    {
        return $this
                ->_getFlagsString($flags, 'AF_', array(AF_SPECIFIC_1, AF_SPECIFIC_2, AF_SPECIFIC_3, AF_SPECIFIC_4, AF_SPECIFIC_5),
                        array('AF_UNIX', 'AF_INET', 'AF_INET6'));
    }

    /**
     * Generates an entity flags string for a given flags value.
     * 
     * @param int $flags flags
     * 
     * @return string flags string
     */

    protected function _getEntityFlagsString($flags)
    {
        return $this->_getFlagsString($flags, 'EF_', array(EF_SPECIFIC_1, EF_SPECIFIC_2, EF_SPECIFIC_3, EF_SPECIFIC_4, EF_SPECIFIC_5));
    }

    /**
     * Checks if a certain attribute should be included or not. Checks both the
     * include and exclude lists. If the attribute is not part of the table 
     * itself it will always be included (e.g. manually added relations etc.)
     *
     * @param atkMetaPolicy $policy meta policy
     * @param string        $name   attribute name
     * 
     * @return bool attribute included?
     */

    protected function _isIncluded(atkMetaPolicy $policy, $name)
    {
        $includes = $policy->getIncludes();
        $excludes = $policy->getExcludes();
        $columns = array_keys($policy->getMetaData());

        if (!in_array($name, $columns))
            return true;
        if ($includes !== NULL && !in_array($name, $includes))
            return false;
        if ($excludes !== NULL && in_array($name, $excludes))
            return false;

        return true;
    }

    /**
     * Compiles the attribute with the given name using the given data.
     *
     * @param atkMetaPolicy $policy meta policy
     * @param string        $name   attribute name
     * @param array         $data   attribute compile data
     * 
     * @param string attribute code
     */

    protected function _compileAttribute(atkMetaPolicy $policy, $name, $data)
    {
        $type = $data['type'];
        $params = $data['params'];
        $flags = isset($data['flags']) ? (int) $data['flags'] : 0;
        $tabs = isset($data['tabs']) ? $data['tabs'] : null;
        $order = $data['order'];

        $code = "// attribute: {$name}\n";

        if (strpos($type, '.') === false && atkexists("attribute", $policy->getEntity()->getModule() . '.' . $type))
            $code .= 'atkuse("attribute", ' . var_export($policy->getEntity()->getModule() . '.' . $type, true) . ");\n";
        elseif (strpos($type, '.') === false && atkexists("relation", $policy->getEntity()->getModule() . '.' . $type))
            $code .= 'atkuse("relation", ' . var_export($policy->getEntity()->getModule() . '.' . $type, true) . ");\n";
        elseif (atkexists("attribute", $type))
            $code .= 'atkuse("attribute", ' . var_export($type, true) . ");\n";
        elseif (atkexists("relation", $type))
            $code .= 'atkuse("relation", ' . var_export($type, true) . ");\n";
        else
            $code .= 'atkimport(' . var_export($type, true) . ");\n";

        if (strrpos($type, ".") !== FALSE)
            $type = substr($type, strrpos($type, ".") + 1);

        $code .= '$attr = new ' . $type . '(' . var_export($name, true);
        foreach ($params as $param)
            $code .= ', ' . var_export($param, true);
        $code .= ");\n";

        if ((Adapto_Config::getGlobal('debug') > 0 || $policy->getEntity()->isCacheable()) && $flags > 0) {
            $flags = $this->_getAttributeFlagsString($flags);
        }

        $code .= "\$attr->addFlag({$flags});\n";

        if (isset($data['forceInsert']) && $data['forceInsert']) {
            $code .= "\$attr->setForceInsert(true);\n";
        }

        if (isset($data['forceUpdate']) && $data['forceUpdate']) {
            $code .= "\$attr->setForceUpdate(true);\n";
        }

        if (isset($data['default'])) {
            $code .= '$attr->setInitialValue(' . var_export($data['default'], true) . ");\n";
        }

        $code .= '$entity->add($attr, ' . var_export($tabs, true) . ', ' . var_export($order, true) . ");\n";

        if (isset($data['column'])) {
            $code .= '$attr->setColumn(' . var_export($data['column'], true) . ");\n";
        }

        $code .= "\n";

        return $code;
    }

    /**
     * Compare the order of two attributes.
     *
     * @param array $attr1 attribute data
     * @param array $attr2 attribute data
     * 
     * @return int comparision value
     */

    public static function compareAttributes($attr1, $attr2)
    {
        if ($attr1['order'] < $attr2['order'])
            return -1;
        else if ($attr1['order'] == $attr2['order'])
            return 0;
        else
            return 1;
    }

    /**
     * Compile attributes.
     *
     * @param atkMetaPolicy $policy meta policy
     * 
     * @return string code
     */

    protected function _compileAttributes(atkMetaPolicy $policy)
    {
        $attrs = $policy->getAttributes();
        uasort($attrs, array(__CLASS__, 'compareAttributes'));

        $code = '';

        if (hasFlag($policy->getEntityFlags(), EF_ML)) {
            $code .= "useattrib('atkmlselectorattribute');\n" . "\$entity->add(new Adapto_MlSelectorAttribute());\n\n";
        }

        foreach ($attrs as $name => $data) {
            if (!array_key_exists('type', $data) || $data['type'] == null || !$this->_isIncluded($policy, $name)) {
                continue;
            }

            $code .= $this->_compileAttribute($policy, $name, $data);
        }

        return $code;
    }

    /**
     * Compile entity base.
     *
     * @param atkMetaPolicy $policy meta policy
     * 
     * @return string code
     */

    protected function _compileBase(atkMetaPolicy $policy)
    {
        $entityFlags = (int) $policy->getEntityFlags();

        if ((Adapto_Config::getGlobal('debug') > 0 || $policy->getEntity()->isCacheable()) && $entityFlags > 0) {
            $entityFlags = $this->_getEntityFlagsString($entityFlags);
        }

        $code = "// initialize entity\n" . "\$entity->setFlags({$entityFlags});\n" . '$entity->setTable(' . var_export($policy->getEntityTable(), true) . ', '
                . var_export($policy->getEntitySequence(), true) . ', ' . var_export($policy->getEntityDatabase(), true) . ");\n";

        if ($policy->getEntityDescriptor() != null)
            $code .= '$entity->setDescriptorTemplate(' . var_export($policy->getEntityDescriptor(), true) . ");\n";

        if ($policy->getEntityOrder() != null)
            $code .= '$entity->setOrder(' . var_export($policy->getEntityOrder(), true) . ");\n";

        if ($policy->getEntityIndex() != null)
            $code .= '$entity->setIndex(' . var_export($policy->getEntityIndex(), true) . ");\n";

        if ($policy->getEntityFilter() != null)
            $code .= '$entity->addFilter(' . var_export($policy->getEntityFilter(), true) . ");\n";

        if ($policy->getEntitySecurityAlias() != null)
            $code .= '$entity->setSecurityAlias(' . var_export($policy->getEntitySecurityAlias(), true) . ");\n";

        if ($policy->getEntitySecurityMap() != null)
            $code .= '$entity->m_securityMap = array_merge($entity->m_securityMap, ' . var_export($policy->getEntitySecurityMap(), true) . ");\n";

        $code .= "\n";

        return $code;
    }

    /**
     * Compile meta policy. Returns the entity code for the given meta policy.
     *
     * @param atkMetaPolicy $policy meta policy
     * 
     * @return string meta policy
     */

    public function compile(atkMetaPolicy $policy)
    {
        $code = $this->_compileBase($policy) . $this->_compileAttributes($policy);

        return $code;
    }
}
