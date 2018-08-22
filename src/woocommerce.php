<?php
namespace App;

if (defined('WC_ABSPATH')) {
    add_action('after_setup_theme', function () {
        add_theme_support('woocommerce');
    });

    add_filter('template_include', function ($template) {
        return strpos($template, WC_ABSPATH) === -1
            ? $template
            : locate_template('woocommerce/' . str_replace(WC_ABSPATH . 'templates/', '', $template)) ?: $template;
    }, 100, 1);

    add_filter('wc_get_template_part', function ($template) {
        $theme_template = locate_template('woocommerce/' . str_replace(WC_ABSPATH . 'templates/', '', $template));

        if ($theme_template) {
            echo template($theme_template);
            return get_stylesheet_directory() . '/index.php';
        }

        return $template;
    }, PHP_INT_MAX, 1);

    add_action('woocommerce_before_template_part', function($template_name, $template_path, $located, $args) {
        $theme_template = locate_template('woocommerce/' . $template_name);

        if ($theme_template) {
            echo template($theme_template, array_merge(
                compact(explode(' ', 'template_name template_path located args')),
                $args
            ));
        }
    }, PHP_INT_MAX, 4);

    add_filter('wc_get_template', function ($template, $template_name, $args) {
        $theme_template = locate_template('woocommerce/' . $template_name);

        // $theme_template already output in woocommerce_before_template_part hook
        return $theme_template ? get_stylesheet_directory() . '/index.php' : $template;
    }, PHP_INT_MAX, 3);
}
