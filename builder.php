<?php

/**
 * A representation of an HTML element.
 * 
 * Can be processed or created by HTML helper functions provided by the builder script.
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
	 * @param bool $condense Whether or not to condense the element. Does not print children when converted to a string.
	 * @param array $attributes A key-value array for all attributes that should be set with this element.
	 */
	function __construct(string $tag, string $text = '', bool $condense = false, array $attributes = []) {
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
	function add_child(html_element $child) {
		array_push($this->children, $child);
	}
	
	/**
	 * Adds multiple children to the array of existing children.
	 * @param array $children The children to be added.
	 * @return void
	 */
	function add_children(array $children) {
		$this->children = array_merge($this->children, $children);
	}
	
	/**
	 * Creates a new `html_element` as a child.
	 * @param string $tag The tag of the element.
	 * @param string $text The inner text of the element, if any.
	 * @param bool $condense Whether or not to condense the element. Does not print children when converted to a string.
	 * @param array $attributes A key-value array for all attributes that should be set with this element.
	 * @return html_element
	 */
	function create_child(string $tag, string $text = '', bool $condense = false, array $attributes = []) {
		$child = new html_element($tag, $text, $condense, $attributes);
		$this->add_child($child);
		return $child;	// All objects are passed by reference
	}
	
	/**
	 * Gets the first child by a given tag.
	 * @param string $tag The tag to search for.
	 * @return html_element|null Returns the first `html_element` matching the tag, or null if none.
	 */
	function get_child_by_tag(string $tag) {
		foreach ($this->children as &$child)
			if ($child->tag == $tag) return $child;
		return null;
	}
	
	/**
	 * Gets all children by a given tag.
	 * @param string $tag The tag to search for.
	 * @return array Returns all `html_element`s that match the tag.
	 */
	function get_children_by_tag(string $tag) {
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
	function get_child_by_attribute(string $attrib, string|null $value) {
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
	function get_children_by_attribute(string $attrib, string|null $value) {
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
	function to_string(int $depth) {
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

/**
 * Quickly sets up a document with common parameters, including a body.
 * @param string $title The title of the webpage.
 * @param string $favicon_path The path of the favicon.
 * @param string $css_path The path of the CSS sheet.
 * @return html_element The main `html` element.
 */
function html_quick_setup(string $title, string $favicon_path, string $css_path) {
	$root = new html_element('html');
	$head = $root->create_child('head');
	$head->create_child('title', $title);
	$head->create_child('link', '', true, [
		'rel' => 'icon',
		'type' => 'image/x-icon',
		'href' => $favicon_path,
	]);
	$head->create_child('link', '', true, [
		'rel' => 'stylesheet',
		'href' => $css_path,
	]);
	$root->create_child('body');
	
	return $root;
}

/**
 * Helper function to create a table based on a 2D array.
 * @param array $contents A 2D array with either strings or `html_element`s as values.
 * @param array $attributes A key-value array for the attributes of the table.
 * @return html_element The `table` element.
 * 
 * @exception InvalidArgumentException Thrown when a value in the 2D array is of an invalid type.
 */
function html_create_table(array $contents, array $attributes = []) {
	$table = new html_element('table', '', false, $attributes);
	foreach ($contents as $rows) {
		$row = $table->create_child('tr');
		foreach ($rows as $row_item) {
			$cell = $row->create_child('td');
			if (is_string($row_item))
				$cell->text = $row_item;
			else if (is_object($row_item) && $row_item instanceof html_element)
				$cell->add_child($row_item);
			else
				throw new InvalidArgumentException("Value in array is not of valid type, must be string or html_element.");
		}
	}
	
	return $table;
}

/**
 * Helper function to create multiple paragraphs from a multiline string.
 * @param string $lines The multiline string.
 * @return html_element[] The `p` elements, with `br` on empty lines.
 */
function html_create_paragraphs(string $lines) {
	if ($lines == '')
		return [];

	$pg_elements = [];
	
	$pg_array = explode("\n", str_replace("\r", '', $lines));
	foreach ($pg_array as $pg_text) {
		if ($pg_text == '')
			$pg_elements[] = new html_element('br', '', true);
		else
			$pg_elements[] = new html_element('p', $pg_text);
	}
	
	return $pg_elements;
}

/**
 * Helper function to create a list of items.
 * @param array $contents An array of strings or `html_element`s.
 * @param bool $ordered Whether or not to use an ordered list.
 * @return html_element The `ol`/`ul` element.
 * 
 * @exception InvalidArgumentException Thrown when a value in the array is of an invalid type.
 */
function html_create_list(array $contents, bool $ordered = false) {
	$list = new html_element($ordered ? 'ol' : 'ul');

	foreach ($contents as $entry) {
		$list_item = $list->create_child('li');
		if (is_string($entry))
			$list_item->text = $entry;
		else if (is_object($entry) && $entry instanceof html_element)
			$list_item->add_child($entry);
		else
			throw new InvalidArgumentException("Value in array is not of valid type, must be string or html_element.");
	}

	return $list;
}

/**
 * Helper function to create an image element.
 * @param string $src The link to the source of the image.
 * @param string $alt_text The alt text of the image, for visually impaired users or when the image fails to load.
 * @param array $attributes A key-value array for additional attributes of the image.
 * @return html_element The `img` element.
 */
function html_create_image(string $src, string $alt_text, array $attributes = []) {
	$img_attributes = array_merge([
		'src' => $src,
		'alt' => $alt_text,
	], $attributes);
	return new html_element('img', '', true, $img_attributes);
}

/**
 * Helper function to create a link element.
 * @param string $href Where the hyperlink should point to.
 * @param string $text The text in the link, if any.
 * @param array $attributes A key-value array for additional attributes of the link.
 * @return html_element The `a` element.
 */
function html_create_link(string $href, string $text = '', array $attributes = []) {
	$lnk_attributes = array_merge([
		'href' => $href,
	], $attributes);
	return new html_element('a', $text, false, $lnk_attributes);
}

/**
 * Prints an entire document to the output.
 * @param html_element $htmlroot The root `html` element.
 * @return void
 */
function html_print(html_element $htmlroot) {
	echo "<!DOCTYPE html>\n" . strval($htmlroot);
}

?>