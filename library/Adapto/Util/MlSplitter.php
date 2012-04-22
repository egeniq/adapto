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
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * This class is responsible for managing multilanguage entitys which have
 * multiple records per occurance.
 * It updates/saves or merges multiple records.
 * This is used by multilingual atkEntitys. It should generally not be
 * necessary to use this class directly.
 *
 * @author Martin Roest <martin@ibuildings.nl>
 * @package adapto
 * @subpackage utils
 *
 */
class Adapto_Util_MlSplitter
{

    /**
     * Constructor
     *
     */
    function __construct()
    {
        // constructor
    }

    /**
     * Retrieve the mlsplitter instance
     * @return atkOutput The instance.
     */
    function &getInstance()
    {
        static $s_mlsplitter = NULL;
        if (!is_object($s_mlsplitter)) {
            $s_mlsplitter = new Adapto_Util_mlsplitter();
            atkdebug("Created a new Adapto_Util_mlsplitter instance");
        }
        return $s_mlsplitter;
    }

    /**
     * Get supported languages
     *
     * @param atkEntity $entity
     * @return array Array with supported languages
     */
    function getLanguages(&$entity)
    {
        if (method_exists($entity, "getLanguages")) {
            return $entity->getLanguages();
        }
        $lngs = Adapto_Config::getGlobal("supported_languages");
        for ($i = 0, $_i = count($lngs); $i < $_i; $i++)
            $lngs[$i] = strtoupper($lngs[$i]);
        return $lngs;
    }

    /**
     * Update the language field
     *
     * @param atkEntity $entity
     * @param array $record
     * @return bool
     */
    function updateLngField(&$entity, &$record)
    {
        // blegh
        $db = &atkGetDb();
        $sql = "UPDATE " . $entity->m_table . " SET " . $entity->m_lngfield . "='" . $entity->m_defaultlanguage . "'
               WHERE " . $entity->m_lngfield . "='' AND " . $record["atkprimkey"];
        return $db->query($sql);
    }

    /**
     * add/update multiple language records
     * because entitys are cached and we have to make some attribute modifications you
     * can't pass the entity as a reference!!
     *
     * @param atkEntity $entity
     * @param array $record
     * @param string $mode
     */
    function updateMlRecords($entity, $record, $mode = "add", $excludes = '', $includes = '')
    {
        atkdebug("Adapto_Util_mlsplitter::updateMlRecords() for mode $mode");

        $excludelist = array();
        $relations = array();

        foreach ($entity->m_attribList as $attribname => $attrib) {
            if (is_subclass_of($entity->m_attribList[$attribname], "atkRelation")) {
                // manytoone relations are stored only when adding
                // assume all onetomanyrelations are stored in the PRESTORE so we MUST NOT use addDb()
                // on this entity if its contains relations to others
                // but for 1:n and n:1 we need to save the refkey
                if ((is_a($entity->m_attribList[$attribname], "atkManyToOneRelation") || is_a($entity->m_attribList[$attribname], "atkOneToOneRelation"))
                        && hasFlag($entity->m_attribList[$attribname]->storageType(), ADDTOQUERY)) {
                    $relations[$attribname] = $entity->m_attribList[$attribname];

                    $p_attrib = &$entity->m_attribList[$attribname];
                    $p_attrib->createDestination();
                    $attribvalue = $p_attrib->m_destInstance->m_attribList[$p_attrib->m_destInstance->primaryKeyField()]->value2db($record[$attribname]);
                    $record[$p_attrib->fieldName()] = $attribvalue;
                    $p_attrib = new Adapto_Attribute($attribname);
                    $p_attrib->m_ownerInstance = &$entity;
                    $p_attrib->init();
                } else
                    $excludelist[] = $attribname;
            }
        }

        $languages = $this->getLanguages($entity);
        $atklngrecordmodes = sessionLoad("atklng_" . $entity->m_type . "_" . atkPrevLevel());

        $autoincrementflags = Array();
        foreach ($entity->m_primaryKey as $primkey) {
            // Make sure we don't increment the primkey
            if ($entity->m_attribList[$primkey]->hasFlag(AF_AUTOINCREMENT))
                $entity->m_attribList[$primkey]->removeFlag(AF_AUTO_INCREMENT);
            $autoincrementflags[] = $primkey;
        }

        foreach ($languages as $language) {
            if ($atklngrecordmodes[$language]["mode"] == "updatelngfield") {
                $this->updateLngField($entity, $record);
            }
            if ($language == $entity->m_defaultlanguage)
                continue;
            foreach ($entity->m_attribList as $attribname => $attrib) {
                if ($entity->m_attribList[$attribname]->hasFlag(AF_ML))
                    $record[$attribname] = $language;
                if ($entity->m_attribList[$attribname]->m_mlattribute) {
                    // change the language of the attribute
                    $entity->m_attribList[$attribname]->m_language = $language;
                }
            }
            $record["atkprimkey"] = $entity->primaryKey($record) . " AND " . $entity->m_table . "." . $entity->m_lngfield . "='$language' ";

            $editMode = $mode;
            if ($atklngrecordmodes[$language]["mode"] == "add") {
                $editMode = "add"; // override the mode in case of missing lngrecords
            }

            // check if we have any locally generated excludes. If needed we merge
            // them with the parameter excludes
            if (count($excludelist)) {
                if (is_array($excludes)) {
                    $excludes = array_unique(array_merge($excludes, $excludelist));
                } else {
                    $excludes = $excludelist;
                }
            }

            switch ($editMode) {
            case "update":
                $entity->updateDb($record, true, $excludes, $includes);
                break;
            default:
                $entity->addDb($record, false, $mode, $excludes);
            }
            $record["atkprimkey"] = $oldprimkey;
        }

        foreach ($entity->m_attribList as $attribname => $attrib) {
            // restore the default language
            if ($entity->m_attribList[$attribname]->m_mlattribute) {
                $entity->m_attribList[$attribname]->m_language = $entity->m_defaultlanguage;
            }
        }

        foreach ($autoincrementflags as $primkey) {
            // restore the attrib flags
            $entity->m_attribList[$primkey]->addFlag(AF_AUTO_INCREMENT);
        }

        foreach ($relations as $attribname => $relation) {
            // restore the relations
            $entity->m_attribList[$attribname] = $relation;
        }
        sessionStore("atklng_" . $entity->m_type . "_" . atkPrevLevel(), NULL); // deleting modes
    }

    /**
     * Adds language condition
     *
     * @param atkQuery $query
     * @param atkEntity $entity
     * @param string $mode
     * @param string $joinalias
     */
    function addMlCondition(&$query, &$entity, $mode, $joinalias)
    {
        global $Adapto_VARS;
        $lng = (isset($Adapto_VARS["atklng"]) ? $Adapto_VARS["atklng"] : "");

        if (!$lng)
            $lng = $entity->m_defaultlanguage;

        if ($entity->hasFlag(EF_ML) && $mode != "edit" && $mode != "copy") {
            $fieldname = $joinalias . "." . $entity->m_lngfield;
            $query->addCondition("({$fieldname} = '' OR {$fieldname} IS NULL OR UPPER({$fieldname})='" . strtoupper($lng) . "')");
        }
    }

    /**
     * merges multiple multilanguage records to one record with fields containing arrays needed by mlattributes
     *
     * @param atkEntity $entity
     * @param array $recordset
     * @param atkQuery $query
     */
    function combineMlRecordSet(&$entity, &$recordset, $query)
    {
        $hasrelationwithmlentity = $this->getMlEntitys($entity);
        $languages = $this->getLanguages($entity);
        if (count($languages) != count($recordset)) {
            $recordset = $this->addLngRecords($entity, $recordset);
        }
        $this->mergeMlRecords($entity, $recordset);

        sessionStore("atklng_" . $entity->m_type . "_" . atkLevel(), $recordset[0]["atklngrecordmodes"]);
    }

    /**
     * this is used to find 1:1 relations with multilanguage support
     * we need these relation because the recordlist will have them included
     * when editting a record we have to combine these records
     *
     * @param atkEntity $entity
     * @return array Array with relationnames
     */
    function getMlEntitys(&$entity)
    {
        // we only have to check the 1:1 relations!!
        $hasrelationwithmlentity = Array();
        if (is_array($entity->m_relations["atkonetoonerelation"])) {
            foreach ($entity->m_relations["atkonetoonerelation"] as $attribname => $attribute) {
                $p_attrib = &$entity->m_attribList[$attribname];
                if ($p_attrib->createDestination() && $p_attrib->m_destInstance->hasFlag(EF_ML)) {
                    $hasrelationwithmlentity[$attribname] = &$entity->m_attriblist[$attribname];
                }
            }
        }
        return $hasrelationwithmlentity;
    }

    /**
     * Has language record?
     *
     * @param atkEntity $entity
     * @param array $recordset
     * @param string $lng
     * @param int $index
     * @return bool
     */
    function hasLngRecord(&$entity, &$recordset, $lng, &$index)
    {
        $index = 0;
        foreach ($recordset as $record) {
            if ($record[$entity->m_lngfield] == $lng)
                return true;
            $index++;
        }
        return false;
    }

    /**
     * Add language records
     *
     * @param atkEntity $entity
     * @param array $recordset
     * @return array Array with records to add
     */
    function addLngRecords(&$entity, &$recordset)
    {
        $newrecordset = Array();
        $languages = $this->getLanguages($entity);
        atkdebug("Adapto_Util_mlsplitter adding missings lngrecord for " . $entity->m_type . "!");
        for ($i = 0, $max = count($languages); $i < $max; $i++) {
            $index = NULL;
            if (!$this->hasLngRecord($entity, $recordset, $languages[$i], $index)) {
                $recordcount = count($newrecordset);
                $newrecordset[$recordcount] = $recordset[0]; // assume that the first record is OK.
                $newrecordset[$recordcount][$entity->m_lngfield] = $languages[$i];

                if ($languages[$i] != $entity->m_defaultlanguage) // saving atkaction
                    $newrecordset[$recordcount]["atklngrecordmodes"][$languages[$i]]["mode"] = "add";
                else
                    $newrecordset[$recordcount]["atklngrecordmodes"][$languages[$i]]["mode"] = "updatelngfield";
            } else
                $newrecordset[] = $recordset[$index];
        }
        return $newrecordset;
    }

    /**
     * Merge multilanguage records
     *
     * @param atkEntity $entity
     * @param array $recordset
     */
    function mergeMlRecords(&$entity, &$recordset)
    {
        $lngattribs = array();
        $lngattribvalues = array();
        foreach ($entity->m_attribList as $attribname => $attrib) {
            if ($entity->m_attribList[$attribname]->m_mlattribute)
                $lngattribs[$attribname] = &$entity->m_attribList[$attribname];
        }
        $i = $this->searchRecordDefaultLanguage($recordset, $entity->m_defaultlanguage);
        $ml_record[0] = $recordset[$i]; // assume this is the record with the default language
        $ml_record[0]["atklngrecordmodes"] = Array();
        for ($i = 0, $max = count($recordset); $i < $max; $i++) {
            if (is_array($recordset[$i]["atklngrecordmodes"])) // keep track off atkactions
                $ml_record[0]["atklngrecordmodes"] = array_merge($ml_record[0]["atklngrecordmodes"], $recordset[$i]["atklngrecordmodes"]);
            foreach ($lngattribs as $lngattribname => $lngattrib) {
                $lngattribvalues[$lngattribname][strtoupper($recordset[$i][$entity->m_lngfield])] = $recordset[$i][$lngattribname];
            }
        }
        foreach ($lngattribvalues as $lngattribname => $value) {
            $ml_record[0][$lngattribname] = $value;
        }
        $recordset = $ml_record;
    }

    /**
     * Search the recordset for the default language
     *
     * @param array $recordset
     * @param string $defaultlanguage
     * @return int The position in the recordset array where the defaultlanguage is found
     */
    function searchRecordDefaultLanguage($recordset, $defaultlanguage)
    {
        for ($i = 0; $i < count($recordset); $i++) {
            if ($recordset[$i]["lng"] == $defaultlanguage)
                return $i;
        }
        return 0;
    }
}
?>
