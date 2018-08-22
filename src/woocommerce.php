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

    add_filter('wc_get_template', function ($template, $template_name, $args) {
        $theme_template = locate_template('woocommerce/' . $template_name);

        // Don't render template when used in REST
        if ($theme_template && !(defined('REST_REQUEST') && REST_REQUEST)) {
            $data = collect(get_body_class())->reduce(function ($data, $class) use ($template) {
                return apply_filters("sage/template/{$class}/data", $data, $template);
            }, []);

            echo template($theme_template, array_merge($data, $args));
            return get_stylesheet_directory() . '/index.php';
        }

        return $theme_template ? template_path($theme_template, $args) : $template;
    }, PHP_INT_MAX, 3);
}
