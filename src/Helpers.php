<?php
namespace HTMLBuild;

/**
 * A helper class for doing various HTML construction operations.
 * 
 * @author Jamesthe1 <jamesthe1sky@gmail.com>
 * @since 3.0.0
 */
class Helpers {
    /**
     * Quickly sets up a document with common parameters, including a body.
     * @param string $title The title of the webpage.
     * @param string $favicon_path The path of the favicon.
     * @param string $css_path The path of the CSS sheet.
     * @return html_element The main `html` element.
     */
    public static function html_quick_setup(string $title, string $favicon_path, string $css_path) {
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
     * @exception \InvalidArgumentException Thrown when a value in the 2D array is of an invalid type.
     */
    public static function html_create_table(array $contents, array $attributes = []) {
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
                    throw new \InvalidArgumentException("Value in array is not of valid type, must be string or html_element.");
            }
        }
        
        return $table;
    }

    /**
     * Helper function to create multiple paragraphs from a multiline string.
     * @param string $lines The multiline string.
     * @return html_element[] The `p` elements, with `br` on empty lines.
     */
    public static function html_create_paragraphs(string $lines) {
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
     * @exception \InvalidArgumentException Thrown when a value in the array is of an invalid type.
     */
    public static function html_create_list(array $contents, bool $ordered = false) {
        $list = new html_element($ordered ? 'ol' : 'ul');

        foreach ($contents as $entry) {
            $list_item = $list->create_child('li');
            if (is_string($entry))
                $list_item->text = $entry;
            else if (is_object($entry) && $entry instanceof html_element)
                $list_item->add_child($entry);
            else
                throw new \InvalidArgumentException("Value in array is not of valid type, must be string or html_element.");
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
    public static function html_create_image(string $src, string $alt_text, array $attributes = []) {
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
    public static function html_create_link(string $href, string $text = '', array $attributes = []) {
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
    public static function html_print(html_element $htmlroot) {
        echo "<!DOCTYPE html>\n" . \strval($htmlroot);
    }
}