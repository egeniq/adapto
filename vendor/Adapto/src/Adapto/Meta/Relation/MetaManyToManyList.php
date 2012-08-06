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
userelation("atkmanytomanylistrelation");

/**
 * Implementation of many-to-many relationships using checkboxes for metan entitys.
 *
 * @author petercv
 * @package adapto
 * @subpackage meta.relations
 */ 
class Adapto_Meta_Relation_MetaManyToManyList extends Adapto_ManyToManyListRelation
{
    private $m_localVariants = null;
    private $m_remoteVariants = null;

    /**
     * Default constructor
     *     
     * @param String $name The name of the relation
     * @param String $destination The full name of the entity that is the other
     *                            end of the relation.
     * @param String $through The full name of the entity that is used as
     *                     intermediairy entity. The intermediairy entity is
     *                     assumed to have 2 attributes that are named
     *                     after the entitys at both ends of the relation.
     *                     For example, if entity 'project' has a M2M relation
     *                     with 'activity', then the intermediairy entity
     *                     'project_activity' is assumed to have an attribute
     *                     named 'project' and one that is named 'activity'.
     *                     You can set your own keys by calling setLocalKey()
     *                     and setRemoteKey()
     * @param string $template The descriptor template
     * @param array $options Array with options
     * @param int $flags Flags for the relation.
     */

    public function __construct($name, $destination, $through, $template, $options, $flags = 0)
    {
        parent::__construct($name, $through, $destination, $flags);

        $this->setDescriptorTemplate($template);
        if (isset($options["filter"]))
            $this->setDestinationFilter($options["filter"]);
        if (isset($options["rows"]))
            $this->setRows($options["rows"]);
        if (isset($options["local"]))
            $this->setLocalKey($options["local"]);
        if (isset($options["localVariants"]))
            $this->m_localVariants = $options['localVariants'];
        if (isset($options["remote"]))
            $this->setRemoteKey($options["remote"]);
        if (isset($options["remoteVariants"]))
            $this->m_remoteVariants = $options['remoteVariants'];
    }

    /**
     * Create the instance of the intermedinary link entity.
     *
     * If succesful, the instance is stored in the m_linkInstance member variable.
     *
     * @return boolean true if succesful, false if something went wrong.
     */

    public function createLink()
    {
        $result = parent::createLink();

        if ($result && is_array($this->m_localVariants)) {
            foreach ($this->m_localVariants as $variant) {
                if ($this->m_linkInstance->getAttribute($variant) != null) {
                    $this->setLocalKey($variant);
                    break;
                }
            }

            $this->m_localVariants = null;
        }

        if ($result && is_array($this->m_remoteVariants)) {
            foreach ($this->m_remoteVariants as $variant) {
                // we *must* use $this->m_linkInstance (not $this->getLink()) else we will create an infinite loop    
                if ($this->m_linkInstance->getAttribute($variant) != null) {
                    $this->setRemoteKey($variant);
                    break;
                }
            }

            $this->m_remoteVariants = null;
        }

        return $result;
    }
}
