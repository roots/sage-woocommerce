<?php
namespace Roots;

if (defined('WC_ABSPATH')) {
    add_action('after_setup_theme', function () {
        add_theme_support('woocommerce');
    });

    /**
     * @param string $template
     * @return string
     */
    function wc_template_loader(String $template)
    {
        if (strpos($template, WC_ABSPATH) === -1) {
            return $template;
        }
        $name = WC()->template_path() . str_replace(WC_ABSPATH . 'templates/', '', $template);
        return locate_template(app('sage.finder')->locate($name)) ? : $template;
    }
    add_filter('template_include', __NAMESPACE__ . '\\wc_template_loader', 90, 1);
    add_filter('comments_template', __NAMESPACE__ . '\\wc_template_loader', 100, 1);

    add_filter('wc_get_template_part', function ($template) {
        $name = WC()->template_path() . str_replace(WC_ABSPATH . 'templates/', '', $template);
        $theme_template = locate_template(app('sage.finder')->locate($name));

        if ($theme_template) {
            $view = app('view.finder')
                ->getPossibleViewNameFromPath($file = realpath($theme_template));

            $view = trim($view, '\\/.');

            /** Gather data to be passed to view */
            $data = array_reduce(get_body_class(), function ($data, $class) use ($view, $file) {
                return apply_filters("sage/template/{$class}/data", $data, $view, $file);
            }, []);

            echo view($view, $data)->render();
        }

        return $template;
    }, PHP_INT_MAX, 1);

    add_action('woocommerce_before_template_part', function ($template_name, $template_path, $located, $args) {
        $name = WC()->template_path() . $template_name;
        $theme_template = locate_template(app('sage.finder')->locate($name));

        if ($theme_template) {
            $view = app('view.finder')
                ->getPossibleViewNameFromPath($file = realpath($theme_template));

            $view = trim($view, '\\/.');

            /** Gather data to be passed to view */
            $data = array_reduce(get_body_class(), function ($data, $class) use ($view, $file) {
                return apply_filters("sage/template/{$class}/data", $data, $view, $file);
            }, []);

            echo view($view, array_merge(
                compact(explode(' ', 'template_name template_path located args')),
                $data,
                $args
            ))->render();
        }
    }, PHP_INT_MAX, 4);

    add_filter('wc_get_template', function ($template, $template_name, $args) {
        $name = WC()->template_path() . $template_name;
        $theme_template = locate_template(app('sage.finder')->locate($name));

        // return theme filename for status screen
        if (is_admin() &&
            ! wp_doing_ajax() &&
            function_exists('get_current_screen') &&
            get_current_screen() &&
            get_current_screen()->id === 'woocommerce_page_wc-status') {
            return $theme_template ? : $template;
        }

        // return empty file, output already rendered by 'woocommerce_before_template_part' hook
        return $theme_template ? get_template_directory() . '/index.php' : $template;
    }, 100, 3);
}
