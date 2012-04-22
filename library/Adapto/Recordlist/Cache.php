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
 * @copyright (c)2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 */

/**
 * RecordlistCaching class
 * This class should take care of all the caching of recordlists.
 * Using this you should be able to dramatically improve the performance of
 * your application.
 *
 * It works by storing the HTML output of recordlist in an 'rlcache' directory
 * in the atktempdir.
 * In addition to this you can specify your own 'identifiers' (in your entity or on the instance)
 * of this class) in a member variable called 'm_cacheidentifiers'.
 * Use these vars to identify between different situations per entity.
 *
 * Example: $this->m_cacheidentifiers=array(array('key'=>'answer','value'=>$answer));
 *
 * @author Boy Baukema <boy@ibuildings.nl>
 * @package adapto
 * @subpackage recordlist
 */
class Adapto_Recordlist_Cache
{
    /**
     * The directory where we store the cache
     * @var String
     * @access private
     */
    public $m_cachedir; // defaulted to public

    /**
     * The full path of the cachefile
     * @var String
     * @access private
     */
    public $m_cacheid; // defaulted to public

    /**
     * The postvars for the recordlist
     * @var Array
     * @access private
     */
    public $m_postvars; // defaulted to public

    /**
     * The entity of the recordlist
     * @var Object
     * @access private
     */
    public $m_entity; // defaulted to public

    /**
     * The cache identifiers
     * These are the variables that make a cacheid unique
     * @var Array
     * @access private
     */
    public $m_cacheidentifiers; // defaulted to public

    /**
     * The constructor
     * This is a singleton, so please use the getInstance method
     * @param Object $entity The entity of the recordlist
     * @param string $postvars The postvars of the recordlist
     * @access private
     */

    public function __construct($entity = "", $postvars = "")
    {
        $this->m_entity = $entity;
        $this->m_postvars = $postvars;
    }

    /**
     * Setter for the entity of the recordlistcache
     * @param Object $entity The entity of the recordlist
     */
    function setEntity($entity)
    {
        $this->m_entity = $entity;
    }

    /**
     * Setter for the postvars of the recordlistcache
     * @param string $postvars The postvars of the recordlist
     */
    function setPostvars($postvars)
    {
        $this->m_postvars = $postvars;
    }

    /**
     * Gets the cache of the recordlist and registers the appropriate javascript
     * @return string The cached recordlist
     */
    function getCache()
    {
        $output = false;
        $this->_setCacheId();

        if (file_exists($this->m_cacheid) && filesize($this->m_cacheid) && !$this->noCaching()) {
            $theme = &atkinstance("atk.ui.atktheme");
            $page = &atkPage::getInstance();

            $page->register_style($theme->stylePath("recordlist.css"));
            $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/formselect.js");
            $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/recordlist.js");

            /**
             * RecordlistCache must call getUniqueId() too, or the counter will be off.
             */
            getUniqueId("normalRecordList");

            $stackID = atkStackID();
            $page->register_loadscript(str_replace("*|REPLACESTACKID|*", $stackID, file_get_contents($this->m_cacheid . "_actionloader")));
            $output = str_replace("*|REPLACESTACKID|*", $stackID, file_get_contents(Adapto_Config::getGlobal("atkroot") . $this->m_cacheid));
        }
        return $output;
    }

    /**
     * Makes sure the m_cachedir and the m_cacheid are properly set
     */
    function _setCacheId()
    {
        $this->m_cachedir = Adapto_Config::getGlobal("atktempdir") . "rlcache/";
        $identifiers = $this->getIdentifiers();
        $this->m_cacheid = $this->m_cachedir . implode("_", $identifiers) . "_" . $this->m_postvars['atkstartat'];
        if (!file_exists($this->m_cachedir) || !is_dir($this->m_cachedir))
            mkdir($this->m_cachedir, 0700);
    }

    /**
     * Writes a cached recordlist to the rlcache directory
     * @param string $output       The HTML output of the recordlist
     * @param string $actionloader The actionloader js part of the recordlist
     */
    function writeCache($output, $actionloader)
    {
        if (!$this->noCaching()) {
            $stackID = atkStackID();
            $output = str_replace($stackID, "*|REPLACESTACKID|*", $output);
            $actionloader = str_replace($stackID, "*|REPLACESTACKID|*", $actionloader);

            if (file_exists($this->m_cacheid))
                unlink($this->m_cacheid);
            $fp = &fopen($this->m_cacheid, "a+");

            if ($fp) {
                fwrite($fp, $output);
                fclose($fp);
            } else {
                return atkerror("Couldn't open {$this->m_cacheid} for writing!");
            }

            $fp = &fopen($this->m_cacheid . "_actionloader", "a+");
            if ($fp) {
                fwrite($fp, $actionloader);
                fclose($fp);
            } else {
                return atkerror("Couldn't open {$this->m_cacheid}_actionloader for writing!");
            }
            atkdebug("New cache created for {$this->m_entity->m_module}.{$this->m_entity->m_type} and written to: $this->m_cacheid");
        }
        return;
    }

    /**
     * Wether or not to use caching
     * We don't cache when we are ordering or searching on a recordlist
     * @return bool Wether or not to use caching
     */
    function noCaching()
    {
        return $this->m_postvars['atkorderby'] || ($this->m_postvars['atksearch'] && Adapto_value_in_array($this->m_postvars['atksearch']))
                || ($this->m_postvars['atksmartsearch'] && Adapto_value_in_array($this->m_postvars['atksmartsearch']));
    }

    /**
     * Clears the current recordlist cache
     */
    function clearCache()
    {

        $cachedir = Adapto_Config::getGlobal("atktempdir") . "rlcache/";
        $atkdirtrav = new Adapto_DirectoryTraverser();

        $identifiers = $this->getIdentifiers();

        foreach ($atkdirtrav->getDirContents($cachedir) as $cachefile) {
            $unsignificant = false;
            if (!empty($identifiers)) {
                foreach ($identifiers as $identifier) {
                    if (!strstr($cachefile, $identifier)) {
                        $unsignificant = true;
                    }
                }
            }
            if (!in_array($cachefile, array(".", "..")) && !$unsignificant) {
                unlink($cachedir . $cachefile);
            }
        }
        atkdebug("Cache for {$this->m_entity->m_module}.{$this->m_entity->m_type} cleared");
    }

    /**
     * Gets all the current identifiers and returns them in an array
     * @return Array The identifiers
     */
    function getIdentifiers()
    {
        $identifiers = array();
        $identifiers[] = $this->m_entity->atkEntityType() . "cache";
        if ($this->m_entity->m_cacheidentifiers) {
            $this->_formatIdentifiers($this->m_entity->m_cacheidentifiers, $identifiers);

        }
        $this->_formatIdentifiers($this->m_cacheidentifiers, $identifiers);
        return $identifiers;
    }

    /**
     * Formats the identifiers in a '_keyvalue' way
     * @param Array $identifiers The identifiers to format
     * @param Array $output The formatted identifiers so far
     * @return Array The formatted identifiers
     */
    function _formatIdentifiers($identifiers, &$output)
    {
        if (count($identifiers) > 0) {
            foreach ($identifiers as $identifier) {
                $output[] = "_" . $identifier['key'] . $identifier['value'];
            }
            return $output;
        }
    }

    /**
     * Adds a cache identifier
     * @param Array $identifier The extra cache identifier
     */
    function addCacheIdentifier($identifier)
    {
        $this->m_cacheidentifiers[] = $identifier;
    }
}

?>
