<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 *
 * @copyright (c) 2004-2008 Peter C. Verhage
 * @copyright (c) 2012 Egeniq
 */

namespace Adapto\Perspective;

use Adapto\Attribute\Primary;

use \Adapto\Meta;
use \Adapto\Attribute;

/**
 * The policy that configures a perspective based on an entity.
 */
class Policy
{

    /**
     * The perspective which is being populated by the policy.
     *
     * @var Perspective
     */
    protected $_perspective = null;

    /**
     * The meta grammar which is being used by the policy.
     *
     * @var Grammar
     */
    protected $_grammar = null;

    /**
     * Constructor.
     *
     * @param atkMetaEntity $entity policy entity
     */
    public function __construct(Perspective $perspective)
    {
        $this->setPerspective($perspective);
    }

    /**
     * Returns the entity for this policy.
     *
     * @return Perspective
     */
    public function getPerspective()
    {
        return $this->_perspective;
    }

    /**
     * Sets the entity for this policy.
     *
     * @param atkEntity $entity policy entity
     */
    public function setPerspective(Perspective $perspective)
    {
        $this->_perspective = $perspective;
    }

    /**
     * Returns the meta grammar.
     *
     * @return Grammar the meta grammar
     */
    public function getGrammar()
    {
        return $this->_grammar;
    }

    /**
     * Sets the meta grammar.
     *
     * @param Grammar $grammar the meta grammar
     */
    public function setGrammar(Grammar $grammar)
    {
        $this->_grammar = $grammar;
    }


    /**
     * @return Field Prepopulated field.
     */
    public function createFieldForAttribute(AbstractAttribute $attribute)
    {
        $name = $attribute->getName();

        if (in_array($name, array("passw", "password")))
        {
            $field = new \Adapto\Field\Text();
            $field->setWidget('\Adapto\Widget\Password');
        }
        else if (in_array($name, array("email", "e-mail")))
        {
            $field = new \Adapto\Field\Text();
            $field->setWidget('\Adapto\Widget\Email');
        }
        else if ($name == 'country')
        {
            $field = new \Adapto\Field\Text();
            $field->setWidget('\Adapto\Widget\CountrySelect');            
        }
        else if ($name == 'timezone')
        {
            $field = new \Adapto\Field\Text();
            $field->setWidget('\Adapto\Widget\TimezoneSelect');
        }
        else if ($name == 'created_at' || $name == 'created_on')
        {
            $field = new \Adapto\Field\TimeStamp();
            $field->setWidget('\Adapto\Widget\TimeStamp');
        }
        else if ($name == 'updated_at' || $name == 'updated_on')
        {
            $field = new \Adapto\Field\TimeStamp();
            $field->setWidget('\Adapto\Widget\TimeStamp');
        }
        else if ($name == 'created_by')
        {
            $field = new \Adapto\Field\Relation\ManyToOne();
            $field->setWidget('\Adapto\Widget\UserSelect');
        }
        else if ($name == 'updated_by')
        {
            $field = new \Adapto\Field\Relation\ManyToOne();
            $field->setWidget('\Adapto\Widget\UserSelect');
        }
        else if ($attribute instanceof \Adapto\Attribute\Number && $attribute->getLength() == 1 &&
                (substr($name, 0, 3) == 'is_' || substr($name, 0, 4) == 'has_'))
        {
            $field = new \Adapto\Field\Boolean();
            $field->setWidget('\Adapto\Widget\Checkbox');
        }
        else
        {
            $class = new $attribute->getDefaultFieldSuggestion();
            $field = new $class();
        }

        return $field;
    }

    public function configurePerspective(AbstractAttribute $attribute)
    {
        $name = $attribute->getName();
        $perspective = $this->getPerspective();
        
        if ($attribute instanceof Primary) {
            $perspective->get($name)->setVisible(Perspective::PERSPECTIVE_NONE);   
        }
        if ($attribute instanceof Text) {
            $perspective->get($name)->setVisible(Perspective::PERSPECTIVE_ALL ^ Perspective::PERSPECTIVE_LIST);
        }
 
        if (in_array($name, array("passw", "password")))
        {
            $perspective->get($name)->setVisible(Perspective::PERSPECTIVE_ALL ^ Perspective::PERSPECTIVE_LIST);
        }

    }


  
}