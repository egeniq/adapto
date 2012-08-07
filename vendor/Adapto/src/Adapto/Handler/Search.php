<?php

/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage handlers
 *
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @copyright (c)2000-2004 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Handler class for the search action of an entity. The handler draws a
 * generic search form for the given entity.
 *
 * The actual search is not performed by this handler. The search values are
 * stored in the default atksearch variables, which the admin page uses to
 * perform the actual search. The search form by default redirects to
 * the adminpage to display searchresults.
 *
 * @author ijansch
 * @author Sandy Pleyte <sandy@achievo.org>
 * @package adapto
 * @subpackage handlers
 * @todo The admin action handler is called when a search is performed. This
 *       should be customizable in the future.
 *
 */

class Adapto_Handler_Search extends Adapto_AbstractSearchHandler
{

    /**
     * The action handler method.
     */
    function action_search()
    {
        if (!empty($this->m_partial)) {
            $this->partial($this->m_partial);
            return;
        }

        // save criteria
        $criteria = $this->fetchCriteria();
        $name = $this->handleSavedCriteria($criteria);

        // redirect to search results and return
        $doSearch = isset($this->m_postvars['atkdosearch']);
        if ($doSearch) {
            $this->redirectToResults();
            return;
        } elseif (!empty($this->m_postvars['atkcancel'])) {
            $url = dispatch_url($this->getPreviousEntity(), $this->getPreviousAction());
            $url = session_url($url, atkLevel() > 0 ? SESSION_BACK : SESSION_REPLACE);

            $this->m_entity->redirect($url);
        }

        $page = &$this->getPage();
        $searcharray = array();

        // load criteria
        if (isset($this->m_postvars['load_criteria'])) {
            if (!empty($name)) {
                $criteria = $this->loadCriteria($name);
                $searcharray = $criteria['atksearch'];
            }
        } elseif (isset($this->m_postvars["atksearch"])) {
            $searcharray = $this->m_postvars["atksearch"];
        }
        $page->addcontent($this->m_entity->renderActionPage("search", $this->invoke("searchPage", $searcharray)));
    }

    /**
     * Redirect to search results based on the given criteria.
     */
    function redirectToResults()
    {
        $url = dispatch_url($this->getPreviousEntity(), $this->getPreviousAction(), $this->fetchCriteria(), atkSelf());
        $url = session_url($url, atkLevel() > 0 ? SESSION_BACK : SESSION_REPLACE);

        $this->m_entity->redirect($url);
    }

    /**
     * Returns the entity from which the search action was called
     *
     * @return string previous entity
     */
    function getPreviousEntity()
    {
        return atkLevel() > 0 ? atkGetSessionManager()->stackVar('atkentitytype', '', atkLevel() - 1) : $this->m_entity->atkEntityType();
    }

    /**
     * Returns the action from which the search action was called
     *
     * @return string previous action
     */
    function getPreviousAction()
    {
        return atkLevel() > 0 ? atkGetSessionManager()->stackVar('atkaction', '', atkLevel() - 1) : 'admin';
    }

    /**
     * Attribute handler.
     *
     * @param string $partial full partial
     */
    function partial_attribute($partial)
    {
        list($type, $attribute, $partial) = explode('.', $partial);

        $attr = &$this->m_entity->getAttribute($attribute);
        if ($attr == NULL) {
            throw new Adapto_Exception("Unknown / invalid attribute '$attribute' for entity '" . $this->m_entity->atkEntityType() . "'");
            return '';
        }

        return $attr->partial($partial, 'add');
    }

    /**
     * This method returns an html page that can be used as a search form.
     * @param array $record A record containing default values that will be
     *                      entered in the searchform.
     * @return String The html search page.
     */
    function searchPage($record = NULL)
    {
        $entity = &$this->m_entity;

        $entity->addStyle("style.css");
        $controller = &atkcontroller::getInstance();
        $controller->setEntity($this->m_entity);

        $page = &$this->getPage();
        $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/tools.js");
        $page->register_script(Adapto_Config::getGlobal("atkroot") . "atk/javascript/formfocus.js");
        $page->register_loadscript("placeFocus();");
        $ui = &$this->getUi();
        if (is_object($ui)) {
            $params = array();
            $params["formstart"] = '<form name="entryform" action="' . $controller->getPhpFile() . '?' . SID . '" method="post">';

            $params["formstart"] .= session_form(SESSION_REPLACE);
            $params["formstart"] .= '<input type="hidden" name="atkaction" value="search">';

            $params["formstart"] .= '<input type="hidden" name="atkentitytype" value="' . $entity->atkentitytype() . '">';
            $params["formstart"] .= '<input type="hidden" name="atkstartat" value="0">'; // start at first page after new search

            $params["content"] = $this->invoke("searchForm", $record);

            $params["buttons"][] = '<input type="submit" class="btn_search atkdefaultbutton" value="' . atktext("search", "atk") . '" name="atkdosearch" >';
            $params["buttons"][] = '<input class="btn_cancel" type="submit" value="' . atktext("cancel", "atk") . '" name="atkcancel"/>';

            $params["formend"] = '</form>';

            $output = $ui->renderAction("search", $params);

            $total = $ui->renderBox(array("title" => $entity->actionTitle('search'), "content" => $output));

            return $total;
        } else {
            throw new Adapto_Exception("ui object failure");
        }
    }

    /**
     * This method returns a form that the user can use to search records.
     *
     * @param array $record A record containing default values to put into
     *                      the search fields.
     * @return String The searchform in html form.
     */
    function searchForm($record = NULL)
    {
        $entity = &$this->m_entity;
        $ui = &$this->getUi();

        if (is_object($ui)) {
            $entity->setAttribSizes();

            $criteria = $this->fetchCriteria();
            $name = $this->handleSavedCriteria($criteria);

            $params = array();
            $params['searchmode_title'] = atktext("search_mode", "atk");
            $params['searchmode_and'] = '<input type="radio" name="atksearchmethod" class="atkradio" value="AND" checked>' . atktext("search_and", "atk");
            $params['searchmode_or'] = '<input type="radio" name="atksearchmethod" class="atkradio" value="OR">' . atktext("search_or", "atk");
            $params['saved_criteria'] = $this->getSavedCriteria($name);

            $params["fields"] = array();

            foreach (array_keys($entity->m_attribList) as $attribname) {
                $p_attrib = &$entity->m_attribList[$attribname];

                if (!$p_attrib->hasFlag(AF_HIDE_SEARCH))
                    $p_attrib->addToSearchformFields($params["fields"], $entity, $record, "", $this->m_postvars['atksearchmode']);
            }
            return $ui->render($entity->getTemplate("search", $record), $params);
        } else
            throw new Adapto_Exception("ui object error");
    }

    /**
     * Fetch posted criteria.
     *
     * @return Array fetched criteria
     */
    function fetchCriteria()
    {
        return array('atksearchmethod' => array_key_exists('atksearchmethod', $this->m_postvars) ? $this->m_postvars['atksearchmethod'] : '',
                'atksearch' => array_key_exists('atksearch', $this->m_postvars) ? $this->m_postvars['atksearch'] : '',
                'atksearchmode' => array_key_exists('atksearchmode', $this->m_postvars) ? $this->m_postvars['atksearchmode'] : '');
    }

}
?>
