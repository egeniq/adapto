<?php 

namespace Lesson1\Controller;

/**
 * Department controller.
 */
class DepartmentsController extends \Adapto\Mvc\Controller\CRUDController
{
    protected $_entityDefClass = '\Lesson1\Model\EntityDef\Department';
    protected $_perspectiveClass = '\Lesson1\Model\Perspective\Department';    
}