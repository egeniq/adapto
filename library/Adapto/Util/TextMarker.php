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
 * @copyright (c)2006 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * The Adapto_Util_TextMarker class
 *
 * @author ijansch
 * @package adapto
 * @subpackage utils
 */
class Adapto_Util_TextMarker
{
    public $m_text = ""; // defaulted to public
    public $m_editedtext = ""; // defaulted to public
    public $m_cutpoints = array(); // defaulted to public

    /**
     * Constructor
     *
     * @param string $text
     * @return Adapto_Util_TextMarker
     */

    public function __construct($text = "")
    {
        $this->setText($text);
    }

    /**
     * Set the text
     *
     * @param string $text
     */
    function setText($text)
    {
        $this->m_editedtext = $this->m_text = $text;
    }

    /**
     * HIde a piece of the text
     *
     * @param int $position The position from where to start hiding text
     * @param int $length The number of characters to hide
     */
    function hide($position, $length)
    {
        $this->m_editedtext = substr($this->m_editedtext, 0, $position) . substr($this->m_editedtext, $position + $length);
        $orgpos = $this->getOriginalPosition($position);
        $this->m_cutpoints[$orgpos] = $length;
    }

    /**
     * Get the text
     *
     * @return string The (edited) text
     */
    function getText()
    {
        return $this->m_editedtext;
    }

    /**
     * Get the original text
     *
     * @return string The original text
     */
    function getOriginalText()
    {
        return $this->m_text;
    }

    /**
     * Get original position
     *
     * @param int $position
     * @return int The original position
     */
    function getOriginalPosition($position)
    {
        $newval = $position;
        foreach ($this->m_cutpoints as $pos => $len) {
            if ($pos <= $position)
                $newval += $len;
        }
        return $newval;
    }
}

?>