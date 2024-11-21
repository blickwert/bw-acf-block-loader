# Blickwert Shortcode Components

**Plugin Name:** Blickwert Shortcode Components  
**Description:** Load custom ACF Blocks and Shortcodes for your WordPress theme.  
**Version:** 0.3  
**Author:** David Wögerer  
**Author URI:** [https://blickwert.at](https://blickwert.at)

## Table of Contents
- [Description](#description)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
  - [ACF Blocks](#acf-blocks)
  - [Shortcodes](#shortcodes)
- [Folder Structure](#folder-structure)
- [Contributing](#contributing)
- [License](#license)

## Description

Blickwert Shortcode Components is a WordPress plugin that allows you to create custom ACF blocks and shortcodes by organizing them into reusable components within your theme. This approach makes it easy to add and manage blocks and shortcodes for different parts of your website, enabling a modular and reusable architecture.

## Features
- Load custom ACF Blocks from your theme directory.
- Register and load shortcodes dynamically from a dedicated directory.
- Automatically enqueue CSS and JS files only when they are needed.
- Modular and reusable folder structure.
- Automated folder creation for easier setup.

## Installation

1. Download the plugin and place it in your WordPress plugins directory.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Ensure your theme has the following folder structure in place:
   - `[theme]/template-part/acf-blocks/` for ACF Blocks.
   - `[theme]/template-part/shortcode/` for Shortcodes.
4. The plugin will automatically create these folders if they do not exist.

## Usage

### ACF Blocks

You can create ACF Blocks by adding them to the `[theme]/template-part/acf-blocks/` directory.

- Each block must be placed in its own subdirectory.
- The subdirectory should contain the following files:
  - `block.php` (required): The template file for the ACF Block.
  - `style.css` (optional): The CSS file for styling the block.
  - `script.js` (optional): The JavaScript file for block functionality.

The `block.php` file should include the block's metadata as comments to register it correctly. Example:

```php
<?php
/**
 * Title: ACF Accordion
 * Description: Block description
 * Version: 0.1
 * Category: formatting
 * Icon: archive
 * Keywords: acf, accordion
 */
// Your block code here
?>
```

### Shortcodes

To create shortcodes, add them to the `[theme]/template-part/shortcode/` directory.

- Each shortcode must be placed in its own subdirectory.
- The subdirectory should contain the following files:
  - `shortcode.php` (required): The template file for the shortcode.
  - `style.css` (optional): The CSS file for styling the shortcode.
  - `script.js` (optional): The JavaScript file for shortcode functionality.

To use a shortcode, add it to a page or post using the `[bw-sc field="shortcode-name"]` syntax.

## Folder Structure

The following folder structure should be used to manage your ACF Blocks and Shortcodes:

```
[theme]/template-part/
  ├── acf-blocks/
  │   ├── block1/
  │   │   ├── block.php
  │   │   ├── style.css (optional)
  │   │   └── script.js (optional)
  │   └── block2/
  │       ├── block.php
  │       ├── style.css (optional)
  │       └── script.js (optional)
  └── shortcode/
      ├── shortcode1/
      │   ├── shortcode.php
      │   ├── style.css (optional)
      │   └── script.js (optional)
      └── shortcode2/
          ├── shortcode.php
          ├── style.css (optional)
          └── script.js (optional)
```

## Contributing

Contributions are welcome! If you have suggestions or improvements, feel free to create an issue or submit a pull request on GitHub.

## License

This plugin is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.

