<?php
namespace HTMLBuild;

/**
 * A representation of an HTML element.
 * 
 * Can be processed or created by HTML helper functions.
 * 
 * @author Jamesthe1 <jamesthe1sky@gmail.com>
 * @since 1.0.0
 * 
 * @property	array	$attributes	A key-value storage for attributes. Keys and values are evaluated with `strval()`.
 * @property	string	$tag		The tag of this element, i.e. `h1` or `a`.
 * @property	string	$text		The inner text of this element. Always precedes any children. If this element will have children, it is recommended for it to keep text to a paragraph element.
 * @property	array	$children	Child elements of this element. It is recommended for all children to be of `html_element`.
 */
class html_element {
	public array $attributes = [];
	public string $tag;
	public string $text;
	public array $children = [];
	
	private bool $condense;
	
	/**
	 * Constructs an html_element.
	 * @param string $tag The tag of the element.
	 * @param string $text The inner text of the element, if any.
	 * @param bool $condense Whether or not to condense the element (i.e. condensing `<br></br>` becomes `<br/>`). Does not print children or contents when converted to a string.
	 * @param array $attributes A key-value array for all attributes that should be set with this element.
	 */
	public function __construct(string $tag, string $text = '', bool $condense = false, array $attributes = []) {
		$this->tag = $tag;
		$this->text = $text;
		$this->attributes = $attributes;
		$this->condense = $condense;
	}
	
	/**
	 * Adds a child to the array of existing children.
	 * @param html_element $child The child to be added.
	 * @return void
	 */
	public function add_child(html_element $child) {
		array_push($this->children, $child);
	}
	
	/**
	 * Adds multiple children to the array of existing children.
	 * @param array $children The children to be added.
	 * @return void
	 */
	public function add_children(array $children) {
		$this->children = array_merge($this->children, $children);	// Preferring array_merge over spread operator because of clearer intent
	}
	
	/**
	 * Creates a new `html_element` as a child.
	 * @param string $tag The tag of the element.
	 * @param string $text The inner text of the element, if any.
	 * @param bool $condense Whether or not to condense the element. Does not print children when converted to a string.
	 * @param array $attributes A key-value array for all attributes that should be set with this element.
	 * @return html_element
	 */
	public function create_child(string $tag, string $text = '', bool $condense = false, array $attributes = []) {
		$child = new html_element($tag, $text, $condense, $attributes);
		$this->add_child($child);
		return $child;	// All objects are passed by reference
	}
	
	/**
	 * Gets the first child by a given tag.
	 * @param string $tag The tag to search for.
	 * @return html_element|null Returns the first `html_element` matching the tag, or null if none.
	 */
	public function get_child_by_tag(string $tag) {
		foreach ($this->children as &$child)
			if ($child->tag == $tag) return $child;
		return null;
	}
	
	/**
	 * Gets all children by a given tag.
	 * @param string $tag The tag to search for.
	 * @return array Returns all `html_element`s that match the tag.
	 */
	public function get_children_by_tag(string $tag) {
		$result = [];
		foreach ($this->children as &$child)
			if ($child->tag == $tag) $result[] = $child;
		return $result;
	}
	
	/**
	 * Gets the first child by a given attribute and its value, if any.
	 * @param string $attrib The attribute to search for.
	 * @param string|null $value The value to search for; skipped if null.
	 * @return html_element|null Returns the first `html_element` matching the search, or null if none.
	 */
	public function get_child_by_attribute(string $attrib, string|null $value) {
		foreach ($this->children as &$child)
			foreach ($child->attributes as $ckey => $cvalue)
				if ($attrib == $ckey && ($value == null || $value == $cvalue)) return $child;
		return null;
	}
	
	/**
	 * Gets all children by a given attribute and its value, if any.
	 * @param string $attrib The attribute to search for.
	 * @param string|null $value The value to search for; skipped if null.
	 * @return array Returns all `html_element`s that match the search.
	 */
	public function get_children_by_attribute(string $attrib, string|null $value) {
		$result = [];
		foreach ($this->children as &$child)
			foreach ($child->attributes as $ckey => $cvalue)
				if ($attrib == $ckey && ($value == null || $value == $cvalue)) $result[] = $child;
		return $result;
	}
	
	/**
	 * Turns the element into a string.
	 * @param int $depth How deep an element is, represented by two tab spaces.
	 * @return string A string representation of the element and its children.
	 */
	public function to_string(int $depth) {
		$attr_txt = '';
		$child_txt = '';
		foreach ($this->attributes as $key => $value) {
			$keystr = strval($key);
			$valuestr = strval($value);
			$attr_txt .= " $keystr='$valuestr'";
		}
		
		foreach ($this->children as $value)
			$child_txt .= $value->to_string($depth + 1);
		
		$tabspace = str_repeat(' ', $depth * 2);
		$begincloser = $this->condense ? ' />' : '>';
		$begin = $tabspace . '<' . $this->tag . $attr_txt . $begincloser . "\n";
		
		if (!$this->condense && $this->text !== '')
			$inner = $tabspace . '  ' . $this->text . "\n" . $child_txt;
		else if (!$this->condense)
			$inner = $child_txt;
		else
			$inner = '';
		
		if (!$this->condense)
			$end = $tabspace . '</' . $this->tag . ">\n";
		else
			$end = '';
		
		return $begin . $inner . $end;
	}

	// For language-based to-string calls like strval()
	function __tostring() {
		return $this->to_string(0);
	}
}

?>