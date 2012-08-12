<?php 

namespace Lesson1\Controller;

/**
 * Department controller.
 */
class DepartmentController extends \Adapto\Mvc\Controller\CRUDController
{
    protected $_entityDefClass = '\Lesson1\Model\EntityDef\Person';
    protected $_formClass = '\Lesson1\Model\UIDef\Person';    
}