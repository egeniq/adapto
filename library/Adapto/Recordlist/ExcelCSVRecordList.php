<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage recordlist
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/** @internal include base class */

/**
 * CVS for Excel recordlist renderer.
 * Note: End of line characters within values are replaced for excel 2003 compatibility reasons!
 *
 * @author Patrick van der Velden <patrick@ibuildings.nl>
 * @package adapto
 * @subpackage recordlist
 *
 */
class Adapto_Recordlist_ExcelCSVRecordList extends Adapto_CustomRecordList
{

    public $lfreplace = null; // defaulted to public

    /**
     * Creates a special Recordlist that can be used for exporting to files or to make it printable
     * @param atkEntity $entity       The entity to use as definition for the columns.
     * @param array $recordset    The records to render
     * @param string $compression        Compression technique (bzip / gzip)
     * @param array $suppressList List of attributes from $entity that should be ignored
     * @param array $outputparams Key-Value parameters for output. Currently existing:
     *                               filename - the name of the file (without extension .csv)
     * @param Boolean $titlerow   Should titlerow be rendered or not
     * @param Boolean $decode     Should data be decoded or not (for exports)
     */
    function render(&$entity, $recordset, $compression = "", $suppressList = "", $outputparams = array(), $titlerow = true, $decode = false)
    {
        parent::render($entity, $recordset, "", "\"", "\"", "\n", "1", $compression, $suppressList, $outputparams, "csv", $titlerow, $decode, ";",
                $this->lfreplace);
    }

    /**
     * Replace line feeds within cell values with another string.
     * Note: Excel 2003 requires this feature for csv imports.
     *
     * @param String $string The line feed replacement string
     */
    function setLfReplacementString($string)
    {
        $this->lfreplace = $string;
    }
}
?>