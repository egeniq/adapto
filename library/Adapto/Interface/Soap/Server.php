<?php
 /**
  * This file is part of the Adapto Toolkit.
  * Detailed copyright and licensing information can be found
  * in the doc/COPYRIGHT and doc/LICENSE files which should be
  * included in the distribution.
  *
  * @package adapto
  * @subpackage interface
  *
  * @copyright (c)2007-2008 Ivo Jansch
  * @copyright (c)2007-2008 Ibuildings.nl BV
  * @license http://www.achievo.org/atk/licensing ATK Open Source License
  *

  */

 /**
  * @todo Replace this with Marks's interface importer.
  */
 include_once(Adapto_Config::getGlobal("atkroot")."atk/interface/interface.atkserverinterface.inc");


 /**
  * The SOAP implementation for the ATK webservices layer.
  * @author ijansch
  * @package adapto
  * @subpackage interface
  */

  class Adapto_Interface_Soap_Server implements atkServerInterface
  {
  	private $m_server = NULL;
  	
  	/**
  	 * Constructor
  	 *
  	 */
  	public function __construct()
  	{
  	  $this->m_server = new SoapServer(null,array("uri"=>"http://".$_SERVER['HTTP_HOST']."/atkdemo/"));
  	  $this->m_server->setObject($this);
  	}
  	
  	/**
  	 * Handle request
  	 *
  	 * @param string $request
  	 * @return String
  	 */
  	public function handleRequest($request)
  	{
  	   return "Hello Soap World";
  	   
  	}
  	
  	/**
  	 * Call the soap function
  	 *
  	 * @param string $method
  	 * @param array $args
  	 */
  	public function __call($method, $args)
  	{
  	  Adapto_Util_Debugger::debug("Function $method called with args: ".var_export($args, true));
  	}
  }
