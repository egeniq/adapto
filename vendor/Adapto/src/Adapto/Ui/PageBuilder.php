<?php
/**
 * This file is part of the Adapto Toolkit.
 * Detailed copyright and licensing information can be found
 * in the doc/COPYRIGHT and doc/LICENSE files which should be
 * included in the distribution.
 *
 * @package adapto
 * @subpackage ui
 *
 * @copyright (c) 2000-2008 Ivo Jansch
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Page builder. Provides a fluent interface to create standardized ATK pages.
 * 
 * $entity->createPageBuilder()
 *      ->title('...')
 *      ->beginActionBox()
 *        ->formStart('...')
 *        ->content('...')
 *      ->endActionBox()
 *      ->box('...')
 *      ->render();
 *
 * @author petercv
 * @package adapto
 * @subpackage ui
 */
class Adapto_Ui_PageBuilder
{
    protected $m_entity = null;
    protected $m_action = null;
    protected $m_record = null;

    protected $m_title = null;
    protected $m_boxes = array();

    /**
     * Constructor.
     *
     * @param atkEntity $entity
     */

    public function __construct(atkEntity $entity)
    {
        $this->m_entity = $entity;
        $this->m_action = $entity->m_action;
    }

    /**
     * Returns the entity.
     *
     * @return atkEntity
     */

    public function getEntity()
    {
        return $this->m_entity;
    }

    /**
     * Sets the action.
     * 
     * @param string $action
     * 
     * @return Adapto_Ui_PageBuilder
     */

    public function action($action)
    {
        $this->m_action = $action;
        return $this;
    }

    /**
     * Sets the record (if applicable) for this action.
     * 
     * @param array $record
     * 
     * @return Adapto_Ui_PageBuilder
     */

    public function record($record)
    {
        $this->m_record = $record;
        return $this;
    }

    /**
     * Sets the page title to the given string.
     *
     * @param string $title
     * 
     * @return atkPage
     */

    public function title($title)
    {
        $this->m_title = $title;
        return $this;
    }

    /**
     * Add box.
     *
     * @param string $content
     * @param string $title
     * @param string $template
     * 
     * @return Adapto_Ui_PageBuilder
     */

    public function box($content, $title = null, $template = null)
    {
        $this->m_boxes[] = array('type' => 'box', 'title' => $title, 'content' => $content, 'template' => $template);
        return $this;
    }

    /**
     * Add action box.
     *
     * @param array  $params
     * @param string $title
     * @param string $template  
     * 
     * @return Adapto_Ui_PageBuilder
     */

    public function actionBox($params, $title = null, $template = null)
    {
        $this->m_boxes[] = array('type' => 'action', 'title' => $title, 'params' => $params, 'template' => $template);
        return $this;
    }

    /**
     * Begins building a new action box.
     *
     * @return atkActionBoxBuilder
     */

    public function beginActionBox()
    {

        return new Adapto_ActionBoxBuilder($this);
    }

    /**
     * Renders the page.
     */

    public function render()
    {
        if ($this->m_title == null) {
            $this->m_title = $this->getEntity()->actionTitle($this->m_action, $this->m_record);
        }

        $boxes = array();
        foreach ($this->m_boxes as $box) {
            $title = $box['title'];
            if ($title == null) {
                $title = $this->m_title;
            }

            if ($box['type'] == 'action') {
                $params = array_merge(array('title' => $title), $box['params']);
                $content = $this->getEntity()->getUi()->renderAction($this->m_action, $params, $this->getEntity()->getModule());
            } else {
                $content = $box['content'];
            }

            $boxes[] = $this->getEntity()->getUi()->renderBox(array('title' => $title, 'content' => $content), $box['template']);
        }

        $this->getEntity()->getPage()->setTitle(atktext('app_shorttitle') . " - " . $this->m_title);

        $content = $this->getEntity()->renderActionPage($this->m_title, $boxes);

        $this->getEntity()->addStyle('style.css');
        $this->getEntity()->getPage()->addContent($content);
        return null;
    }
}
