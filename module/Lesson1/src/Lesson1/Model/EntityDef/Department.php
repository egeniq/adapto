<?php

use \Adapto\EntityDef;
use \Adapto\Attribute;


class Department extends AbstractEntityDef
{
    protected function _init()
    {
        $this->add(new Text("name"))
             ->add(new Text("description"));
    }
}