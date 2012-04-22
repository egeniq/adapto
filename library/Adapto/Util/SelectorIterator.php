<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage utils
 *
 * @copyright (c) 2010 petercv
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Selector iterator, makes sure that each each row returned by the internal
 * iterator gets transformed before it is returned to the user.
 *
 * @author petercv
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_SelectorIterator extends IteratorIterator
{
    /**
     * Selector.
     *
     * @var atkSelector
     */
    private $m_selector;

    /**
     * Constructor.
     *
     * @param Iterator    $iterator iterator
     * @param atkSelector $selector selector
     */

    public function __construct(Iterator $iterator, atkSelector $selector)
    {
        parent::__construct($iterator);
        $this->m_selector = $selector;
    }

    /**
     * Returns the selector.
     *
     * @return atkSelector selector
     */

    public function getSelector()
    {
        return $this->m_selector;
    }

    /**
     * Returns the current row transformed.
     */

    public function current()
    {
        $row = parent::current();

        if ($row != null) {
            $row = $this->getSelector()->transformRow($row);
        }

        return $row;
    }
}
