<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be 
 * included in the distribution.
 *
 * @package adapto
 * @subpackage meta.relations
 *
 * @copyright (c) 2005 petercv
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * @internal Include the base class.
 */
userelation("atkonetoonerelation");

/**
 * Implementation of one-to-one relationships for metan entitys.
 *
 * @author petercv
 * @package adapto
 * @subpackage meta.relations
 */ 
class Adapto_Meta_Relation_MetaOneToOne extends Adapto_OneToOneRelation
{
    private $m_variants;

    /**
     * Default constructor. 
     * 
     * @param String $name The unique name of the attribute. In slave mode,
     *                     this corresponds to the foreign key field in the
     *                     database table.  (The name is also used as the section 
     *                     heading.)
     * @param String $destination the destination entity (in module.entityname
     *                            notation)
     * @param string $template The descriptor template
     * @param array $options Array with options 
     * @param int $flags Attribute flags that influence this attributes'
     *                   behavior.   
     */

    public function __construct($name, $destination, $template, $options, $flags = 0)
    {
        $refKey = isset($options['dest']) ? $options['dest'] : (isset($options['destination']) ? $options['destination'] : '?');
        parent::__construct($name, $destination, $refKey, $flags);

        $this->m_variants = (array) @$options['variants'];

        $this->setDescriptorTemplate($template);

        if (isset($options["filter"]))
            $this->setDestinationFilter($options["filter"]);
    }

    /**
     * Create the instance of the destination.
     *
     * If succesful, the instance is stored in the m_destInstance member variable.
     *
     * @return boolean true if succesful, false if something went wrong.
     */

    public function createDestination()
    {
        $result = parent::createDestination();

        if ($result && is_array($this->m_refKey) && in_array('?', $this->m_refKey)) {
            foreach ($this->m_variants as $variant) {
                // we *must* use $this->m_destInstance (not $this->getDestination()) else we will create an infinite loop    
                if ($this->m_destInstance->getAttribute($variant) != null) {
                    $this->m_refKey = array($variant);
                }
            }
        }

        return $result && $this->m_refKey != NULL;
    }
}
