<?php

namespace \Adapto\Field;

interface FieldInterface
{    
    public function isEditable(int $type);
 	public function setEditable(int $types); // ADD, EDIT, LIST, true, false
    public function isVisible(int $type);
 	public function setVisible(int $types); // ADD, EDIT, LIST, VIEW, true, false
 	public function isSearchable(int $type);
 	public function setSearchable(int $searchable); // LIST, SEARCH, true, false
 	public function isSortable();
 	public function setSortable(boolean $sortable);
 	public function isLabelVisible();
 	public function setLabelVisible(boolean $visible);
 	public function getTotalCallback();
 	public function setTotalCallback($callback); // function($prev, $current, $index) { return $prev - $current; }
 	public function getDefaultValue();
 	public function setDefaultValue($value); // override default in entity
    public function setSection($name);

    public function moveBefore($name); // convenience, calls UIDef
 	public function moveAfter($name); // convenience, calls UIDef
 	public function moveToTop(); // convenience, calls UIDef
 	public function moveToBottom(); // convenience, calls UIDef
    
    public function serialize();
}