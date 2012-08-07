<?php

namespace Adapto\Menu\Item;

abstract class AbstractItem
{
    abstract function getTitle();
    abstract function getLink();
}