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
 * @copyright (c)2000-2004 Ibuildings.nl BV
 * @license http://www.achievo.org/atk/licensing ATK Open Source License
 *

 */

/**
 * Class that generates an index page.
 * @author Boy Baukema <boy@ibuildings.nl>
 * @package adapto
 * @subpackage ui
 */
class Adapto_Ui_IndexPage
{
    /**
     * @var atkPage
     */
    public $m_page; // defaulted to public

    /**
     * @var atkTheme
     */
    public $m_theme; // defaulted to public

    /**
     * @var atkUi
     */
    public $m_ui; // defaulted to public

    /**
     * @var atkOutput
     */
    public $m_output; // defaulted to public

    /**
     * @var Array
     */
    public $m_user; // defaulted to public

    public $m_topsearchpiece; // defaulted to public
    public $m_topcenterpiecelinks; // defaulted to public
    public $m_title; // defaulted to public
    public $m_extrabodyprops; // defaulted to public
    public $m_extraheaders; // defaulted to public
    public $m_username; // defaulted to public
    public $m_defaultDestination; // defaulted to public
    public $m_flags; // defaulted to public

    /**
     * Hide top / menu?
     * 
     * @var boolean
     */
    private $m_noNav;

    /**
     * Constructor
     *
     * @return Adapto_Ui_IndexPage
     */

    public function __construct()
    {
        global $Adapto_VARS;
        $this->m_page = &atkinstance("atk.ui.atkpage");
        $this->m_ui = &atkinstance("atk.ui.atkui");
        $this->m_theme = &atkinstance('atk.ui.atktheme');
        $this->m_output = &atkinstance('atk.ui.atkoutput');
        $this->m_user = getUser();
        $this->m_flags = array_key_exists("atkpartial", $Adapto_VARS) ? HTML_PARTIAL : HTML_STRICT;
        $this->m_noNav = isset($Adapto_VARS['atknonav']);
    }

    /**
     * Does the Adapto_Ui_IndexPage has this flag?
     *
     * @param integer $flag The flag
     * @return Boolean
     */
    function hasFlag($flag)
    {
        return hasFlag($this->m_flags, $flag);
    }

    /**
     * Generate the indexpage
     *
     */
    function generate()
    {
        if (!$this->hasFlag(HTML_PARTIAL) && !$this->m_noNav) {
            $this->atkGenerateTop();
            $this->atkGenerateMenu();
        }

        $this->atkGenerateDispatcher();

        $this->m_output
                ->output(
                        $this->m_page
                                ->render($this->m_title != "" ? $this->m_title : null, $this->m_flags,
                                        $this->m_extrabodyprops != "" ? $this->m_extrabodyprops : null,
                                        $this->m_extraheaders != "" ? $this->m_extraheaders : null));
        $this->m_output->outputFlush();
    }

    /**
     * Generate the menu
     *
     */
    function atkGenerateMenu()
    {
        /* general menu stuff */
        /* load menu layout */

        $menu = &atkMenu::getMenu();

        if (is_object($menu))
            $this->m_page->addContent($menu->getMenu());
        else
            atkerror("no menu object created!");
    }

    /**
     * Generate the top with login text, logout link, etc.
     *
     */
    function atkGenerateTop()
    {
        $logoutLink = Adapto_Config::getGlobal('dispatcher') . '?atklogout=1';

        $this->m_page->register_style($this->m_theme->stylePath("style.css"));
        $this->m_page->register_style($this->m_theme->stylePath("top.css"));

        //Backwards compatible $content, that is what will render when the box.tpl is used instead of a top.tpl
        $loggedin = atkText("logged_in_as", "atk") . ": <b>" . ($this->m_user["name"] ? $this->m_user['name'] : 'administrator') . "</b>";
        $content = '<br />' . $loggedin . ' &nbsp; <a href="' . $logoutLink . '">' . ucfirst(atkText("logout")) . ' </a>&nbsp;<br /><br />';

        $top = $this->m_ui
                ->renderBox(
                        array("content" => $content, "logintext" => atktext("logged_in_as"), "logouttext" => ucfirst(atkText("logout", "atk")),
                                "logoutlink" => $logoutLink, "logouttarget" => "_top", "centerpiece_links" => $this->m_topcenterpiecelinks,
                                "searchpiece" => $this->m_topsearchpiece, "title" => ($this->m_title != "" ? $this->m_title : atkText("app_title")),
                                "user" => ($this->m_username ? $this->m_username : $this->m_user["name"]), "fulluser" => $this->m_user), "top");
        $this->m_page->addContent($top);
    }

    /**
     * Set the top center piece links
     *
     * @param string $centerpiecelinks
     */
    function setTopCenterPieceLinks($centerpiecelinks)
    {
        $this->m_topcenterpiecelinks = $centerpiecelinks;
    }

    /**
     * Set the top search piece
     *
     * @param string $searchpiece
     */
    function setTopSearchPiece($searchpiece)
    {
        $this->m_topsearchpiece = $searchpiece;
    }

    /**
     * Set the title of the page
     *
     * @param string $title
     */
    function setTitle($title)
    {
        $this->m_title = $title;
    }

    /**
     * Set the extra body properties of the page
     *
     * @param string $extrabodyprops
     */
    function setBodyprops($extrabodyprops)
    {
        $this->m_extrabodyprops = $extrabodyprops;
    }

    /**
     * Set the extra headers of the page
     *
     * @param string $extraheaders
     */
    function setExtraheaders($extraheaders)
    {
        $this->m_extraheaders = $extraheaders;
    }

    /**
     * Set the username
     *
     * @param string $username
     */
    function setUsername($username)
    {
        $this->m_username = $username;
    }

    /**
     * Generate the dispatcher
     *
     */
    function atkGenerateDispatcher()
    {
        global $Adapto_VARS;
        $session = &atkSessionManager::getSession();

        if ($session["login"] != 1) {
            // no entitytype passed, or session expired
            $this->m_page->register_style($this->m_theme->stylePath("style.css"));

            $destination = "";
            if (isset($Adapto_VARS["atkentitytype"]) && isset($Adapto_VARS["atkaction"])) {
                $destination = "&atkentitytype=" . $Adapto_VARS["atkentitytype"] . "&atkaction=" . $Adapto_VARS["atkaction"];
                if (isset($Adapto_VARS["atkselector"]))
                    $destination .= "&atkselector=" . $Adapto_VARS["atkselector"];
            }

            $box = $this->m_ui
                    ->renderBox(
                            array("title" => atkText("title_session_expired"),
                                    "content" => '<br><br>' . atkText("explain_session_expired")
                                            . '<br><br><br><br>
                                           <a href="index.php?atklogout=true' . $destination . '" target="_top">' . atkText("relogin") . '</a><br><br>'));

            $this->m_page->addContent($box);

            $this->m_output->output($this->m_page->render(atkText("title_session_expired"), true));
        } else {
            $lockType = Adapto_Config::getGlobal("lock_type");
            if (!empty($lockType))
                atklock();

            // Create entity
            if (isset($Adapto_VARS['atkentitytype'])) {
                $obj = &getEntity($Adapto_VARS['atkentitytype']);

                if (is_object($obj)) {
                    $controller = &atkinstance("atk.atkcontroller");
                    $controller->invoke("loadDispatchPage", $Adapto_VARS);
                } else {
                    atkdebug("No object created!!?!");
                }
            } else {

                if (is_array($this->m_defaultDestination)) {
                    $controller = &atkinstance("atk.atkcontroller");
                    $controller->invoke("loadDispatchPage", $this->m_defaultDestination);
                } else {
                    $this->m_page->register_style($this->m_theme->stylePath("style.css"));
                    $box = $this->m_ui
                            ->renderBox(
                                    array("title" => atkText("app_shorttitle"), "content" => "<br /><br />" . atkText("app_description") . "<br /><br />"));

                    $this->m_page->addContent($box);
                }
            }
        }
    }

    /**
     * Set the default destination
     *
     * @param string $destination The default destination
     */
    function setDefaultDestination($destination)
    {
        if (is_array($destination))
            $this->m_defaultDestination = $destination;
    }
}

?>
