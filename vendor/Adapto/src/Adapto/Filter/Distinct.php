<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be 
 * included in the distribution.
 *
 * @package adapto
 * @subpackage filters
 *
 * @copyright (c)2007 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */ 

/** @internal include baseclass */
usefilter("atkfilter");

/**
 * Add a distinct clause to a query.
 *
 * Use this filter, like you use an attribute, for example:
 * $this->add(new Adapto_Filter_Distinct());
 * 
 * @author ijansch
 * @package adapto
 * @subpackage filters
 *
 */
class Adapto_Filter_Distinct extends Adapto_Filter
{
    /**
     * constructor
     */

    public function __construct()
    {
        parent::__construct("distinctfilter");
    }

    /**
     * add the distinct statement to the query
     *
     * @param atkQuery $query The SQL query object
     * @return void
     */
    function addToQuery(&$query)
    {
        $query->setDistinct(true);
    }
}
?>
