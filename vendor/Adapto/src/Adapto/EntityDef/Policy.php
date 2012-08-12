<?php

namespace Adapto\EntityDef;

class Policy
{
    /**
     * Find destination entity for the given meta relation.
     * TODO: this hasn't been adapto-ized yet. Also it should go in Entity/Policy, not in
     * field policy.
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
    
            return Adapto_ClassLoader::create('atk.meta.atkmetaattributemodifier', $this, $names);
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