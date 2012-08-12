<?php

namespace \Adapto\Field;

class Text extends AbstractField
{
    protected function _createWidget()
    {
        return new \Adapto\Widget\Text($this->getName());
    }
}