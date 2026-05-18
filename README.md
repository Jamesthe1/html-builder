# HTML Builder

This composer package creates a simple HTML builder for PHP.

# API

The API can be found in the single `builder.php` file, as it contains detailed doc comments in the form of DocBlocks.

# Example

```php
$htmlroot = html_quick_setup("My website", "./favicon.ico", "./style.css");
$body = $htmlroot->get_child_by_tag("body"); // All objects are passed by reference.
$body->add_child(new html_element("p", "Hello world!", $attributes = [
    'class' = 'main-text',
    'id' = 'first-text'
]));

html_print($htmlroot);
```