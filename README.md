# ACF Autoincrement Field

## Description

ACF Autoincrement Field is a WordPress plugin that extends the functionality of Advanced Custom Fields (ACF) by adding a custom autoincrement field type. This plugin allows you to automatically generate and assign incremental numeric values to specified fields across various post types.

Developed with the assistance of AI technology (Claude from Anthropic), this plugin offers a flexible and robust solution for managing autoincrementing fields in WordPress.

## Features

- Custom autoincrement field type for ACF
- Support for multiple autoincrement fields across different post types
- Configurable number patterns (e.g., '000000000' for 9-digit numbers)
- Compatible with both ACF-defined fields and custom-configured fields
- Automatic generation of incremental values on post creation and update
- Debug logging for easy troubleshooting

## Requirements

- WordPress 5.0 or higher
- Advanced Custom Fields 5.0 or higher
- PHP 7.0 or higher

## Installation

1. Download the plugin zip file.
2. Log in to your WordPress admin panel and navigate to Plugins > Add New.
3. Click on the "Upload Plugin" button and select the downloaded zip file.
4. Click "Install Now" and then "Activate" to enable the plugin.

## Configuration

The plugin can be configured to work with multiple autoincrement fields across different post types. To configure the fields, modify the `load_config()` method in the main plugin file:

```php
private function load_config() {
    $this->autoincrement_fields = [
        [
            'post_type' => 'membership',
            'field_name' => 'numerotessera',
            'pattern' => '000000000'
        ],
        // Add more configurations here as needed
    ];
}
```

Each configuration array should include:
- `post_type`: The WordPress post type to apply the autoincrement field to.
- `field_name`: The name of the field that will store the autoincrement value.
- `pattern`: The number pattern to use (e.g., '000000000' for a 9-digit number).

## Usage

Once configured, the plugin will automatically handle the generation and assignment of autoincrement values for the specified fields. No additional action is required in your theme or other plugins.

To use the autoincrement field in your ACF field groups:

1. Create a new ACF field group or edit an existing one.
2. Add a new field and select "Autoincrement" as the field type.
3. Configure the field settings as needed.
4. Save the field group.

## Debugging

The plugin includes debug logging functionality. To enable debug logs:

1. Set the `$debug` variable to `true` in the main plugin file:

```php
private $debug = true; // Set to false in production
```

2. Check your WordPress debug log file (usually located at `wp-content/debug.log`) for detailed information about the plugin's operations.

## Contributing

Contributions to the ACF Autoincrement Field plugin are welcome! Please feel free to submit pull requests or create issues on the GitHub repository.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- Developed by Gaetano Di Benedetto
- AI assistance provided by Claude from Anthropic

## Support

For support, please create an issue on the GitHub repository or contact the plugin developer.

---

Thank you for using ACF Autoincrement Field!
