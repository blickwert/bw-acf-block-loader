<?php
/*
Plugin Name: Custom ACF-Block-Loader via Theme
Plugin URI: https://blickwert.at
Description: Load custom ACF Blocks and Shortcodes.
Author: David W&ouml;gerer
Version: 0.3
Author URI: https://blickwert.at
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class BW_Shortcode_Components {

    const ACF_BLOCKS_DIR = '/template-part/acf-blocks/';
    const SHORTCODES_DIR = '/template-part/shortcode/';
    private $theme_dir;
    private $theme_uri;
    private $acf_blocks_dir;
    private $acf_blocks_uri;
    private $shortcodes_dir;
    private $shortcodes_uri;

    public function __construct() {
        $this->theme_dir = get_template_directory();
        $this->theme_uri = get_template_directory_uri();

        $this->acf_blocks_dir = $this->theme_dir . self::ACF_BLOCKS_DIR;
        $this->acf_blocks_uri = $this->theme_uri . self::ACF_BLOCKS_DIR;

        $this->shortcodes_dir = $this->theme_dir . self::SHORTCODES_DIR;
        $this->shortcodes_uri = $this->theme_uri . self::SHORTCODES_DIR;

        // Überprüfe und erstelle Verzeichnisse
        $this->ensure_directory($this->acf_blocks_dir);
        $this->ensure_directory($this->shortcodes_dir);

        // Registriere Aktionen und Shortcodes
        add_action('init', [$this, 'register_shortcodes']);
        add_action('init', [$this, 'register_acf_blocks']);
        add_action('wp_enqueue_scripts', [$this, 'register_widget_assets']);
        add_action('admin_enqueue_scripts', [$this, 'register_widget_assets']);

        // Lade zusätzliche PHP-Dateien
        $this->include_widget_files('functions.php');
    }

    private function ensure_directory($dir) {
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }

    public function register_shortcodes() {
        add_shortcode('bw-sc', [$this, 'handle_shortcode']);
    }

    public function handle_shortcode($atts) {
        $atts = shortcode_atts(['field' => false], $atts);
        $shortcode_name = sanitize_file_name($atts['field']);
        $shortcode_dir = $this->shortcodes_dir . $shortcode_name;

        // Dynamisches Laden von CSS und JS
        $this->enqueue_assets($shortcode_dir);

        // Shortcode-Template rendern
        $shortcode_file = $shortcode_dir . '/shortcode.php';
        if (file_exists($shortcode_file)) {
            ob_start();
            include $shortcode_file;
            return ob_get_clean();
        }

        return '';
    }

    public function register_acf_blocks() {
        if (!function_exists('acf_register_block_type')) {
            return;
        }

        $blocks = $this->get_acf_block_names();
        foreach ($blocks as $block_name) {
            $block_dir = $this->acf_blocks_dir . $block_name;
            $block_file = $block_dir . '/block.php';

            $headers = $this->get_block_headers($block_file);

            if ($headers) {
                acf_register_block_type([
                    'name'              => $block_name,
                    'title'             => __($headers['title']),
                    'description'       => __($headers['desc']),
                    'render_callback'   => [$this, 'render_acf_block'],
                    'category'          => $headers['cat'],
                    'icon'              => $headers['icon'],
                    'keywords'          => explode(',', $headers['keywords']),
                    'mode'              => 'edit',
                    'supports'          => [
                        'align' => true,
                        'mode' => true,
                        'jsx' => true,
                    ],
                    'enqueue_assets'    => function() use ($block_dir) {
                        $this->enqueue_assets($block_dir);
                    },
                ]);
            }
        }
    }

    public function render_acf_block($block) {
        $block_name = str_replace('acf/', '', $block['name']);
        $block_dir = $this->acf_blocks_dir . $block_name;

        $block_file = $block_dir . '/block.php';
        if (file_exists($block_file)) {
            include $block_file;
        }
    }

    public function register_widget_assets() {
        // Assets werden dynamisch geladen, hier werden sie nur registriert
        $this->register_assets_in_dir($this->acf_blocks_dir);
        $this->register_assets_in_dir($this->shortcodes_dir);
    }

    private function register_assets_in_dir($dir) {
        $css_files = $this->get_files_in_dir($dir, 'style.css');
        $js_files = $this->get_files_in_dir($dir, 'script.js');

        foreach ($css_files as $file) {
            $handle = 'bw-style-' . md5($file);
            $url = str_replace($this->theme_dir, $this->theme_uri, $file);
            wp_register_style($handle, $url, [], filemtime($file));
        }

        foreach ($js_files as $file) {
            $handle = 'bw-script-' . md5($file);
            $url = str_replace($this->theme_dir, $this->theme_uri, $file);
            wp_register_script($handle, $url, ['jquery'], filemtime($file), true);
        }
    }

    private function enqueue_assets($dir) {
        $css_file = $dir . '/style.css';
        if (file_exists($css_file)) {
            $handle = 'bw-style-' . md5($css_file);
            wp_enqueue_style($handle);
        }

        $js_file = $dir . '/script.js';
        if (file_exists($js_file)) {
            $handle = 'bw-script-' . md5($js_file);
            wp_enqueue_script($handle);
        }
    }

    private function include_widget_files($filename) {
        $files = array_merge(
            $this->get_files_in_dir($this->acf_blocks_dir, $filename),
            $this->get_files_in_dir($this->shortcodes_dir, $filename)
        );

        foreach ($files as $file) {
            include_once $file;
        }
    }

    private function get_acf_block_names() {
        $blocks = [];
        foreach (glob($this->acf_blocks_dir . '*', GLOB_ONLYDIR) as $dir) {
            $block_name = basename($dir);
            $block_file = $dir . '/block.php';
            if (file_exists($block_file)) {
                $blocks[] = $block_name;
            }
        }
        return $blocks;
    }

    private function get_block_headers($file) {
        if (!file_exists($file)) {
            return null;
        }

        $default_headers = [
            'title'    => 'Title',
            'desc'     => 'Description',
            'Version'  => 'Version',
            'cat'      => 'Category',
            'icon'     => 'Icon',
            'keywords' => 'Keywords',
        ];

        return get_file_data($file, $default_headers);
    }

    private function get_files_in_dir($dir, $filename) {
        $files = [];
        foreach (glob($dir . '*/' . $filename) as $file) {
            $files[] = $file;
        }
        return $files;
    }
}

// Initialisiere das Plugin
new BW_Shortcode_Components();
