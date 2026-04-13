<?php
    
    /**
     * Theme setup.
     */
    
    namespace App;
    
    use Illuminate\Support\Facades\Vite;
    
    /**
     * Inject styles into the block editor.
     *
     * @return array
     */
    add_filter('block_editor_settings_all', function ($settings) {
        $style = Vite::asset('resources/css/editor.css');
        if ($style) {
            $settings['styles'][] = [
                'css' => "@import url('{$style}')",
            ];
        }
        return $settings;
    });
    
    /**
     * Inject scripts into the block editor.
     *
     * @return void
     */
    add_action('admin_head', function () {
        if (!get_current_screen()?->is_block_editor()) {
            return;
        }
        
        $dependencies = json_decode(Vite::content('editor.deps.json'));
        
        foreach ($dependencies as $dependency) {
            if (!wp_script_is($dependency)) {
                wp_enqueue_script($dependency);
            }
        }
        
        echo Vite::withEntryPoints([
            'resources/js/editor.js',
        ])->toHtml();
    });

    /**
     * Font Awesome (front end and block editor) for icons in blocks and templates.
     */
    add_action('wp_enqueue_scripts', function () {
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
            [],
            '6.5.1'
        );
    });
    add_action('admin_enqueue_scripts', function ($hook) {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        wp_enqueue_style(
            'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
            [],
            '6.5.1'
        );
    });

    /**
     * Register the initial theme setup.
     *
     * @return void
     */
    add_action('after_setup_theme', function () {
        /**
         * Disable full-site editing support.
         *
         * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
         */
        remove_theme_support('block-templates');
        
        /**
         * Register the navigation menus.
         *
         * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
         */
        register_nav_menus([
            'header-top-nav'  => __('Header top (rechts)', 'sage'),
            'header-main-nav' => __('Header hoofdmenu', 'sage'),
            'footer-nav-1'    => __('Footer kolom 1', 'sage'),
            'footer-nav-2'    => __('Footer kolom 2', 'sage'),
            'footer-nav-3'    => __('Footer kolom 3', 'sage'),
            'footer-nav-4'    => __('Footer kolom 4', 'sage'),
            'footer-nav-5'    => __('Footer kolom 5', 'sage'),
            'copyright-nav'   => __('Footer: copyright & juridisch (onderaan kaart)', 'sage'),
        ]);
        
        /**
         * Disable the default block patterns.
         *
         * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
         */
        remove_theme_support('core-block-patterns');
        
        /**
         * Enable plugins to manage the document title.
         *
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
         */
        add_theme_support('title-tag');
        
        /**
         * Enable post thumbnail support.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');
        
        /**
         * Enable responsive embed support.
         *
         * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
         */
        add_theme_support('responsive-embeds');
        
        /**
         * Enable HTML5 markup support.
         *
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
         */
        add_theme_support('html5', [
            'caption',
            'comment-form',
            'comment-list',
            'gallery',
            'search-form',
            'script',
            'style',
        ]);
        
        /**
         * Enable selective refresh for widgets in customizer.
         *
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
         */
        add_theme_support('customize-selective-refresh-widgets');
    }, 20);

    add_action('wp_head', function () {
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    }, 1);

    /**
     * Organization + WebSite rich results (site-wide).
     */
    add_action('wp_head', function () {
        $name = get_bloginfo('name');
        $url = home_url('/');
        $description = get_bloginfo('description');
        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $url . '#organization',
                    'name' => $name,
                    'url' => $url,
                    ...($description ? ['description' => $description] : []),
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $url . '#website',
                    'url' => $url,
                    'name' => $name,
                    ...($description ? ['description' => $description] : []),
                    'publisher' => ['@id' => $url . '#organization'],
                ],
            ],
        ];
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
    }, 5);
    
    /**
     * Init – ACF, image sizes, comments, TinyMCE.
     */
    add_action('init', function () {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page();
        }

        add_image_size('big', 600, 600, false);
        add_image_size('full', 2000, 2000, false);

        foreach (get_intermediate_image_sizes() as $size) {
            if (!in_array($size, ['thumbnail', 'medium', 'big', 'large', 'full'])) {
                remove_image_size($size);
            }
        }

        remove_post_type_support('post', 'comments');
        remove_post_type_support('page', 'comments');

        add_filter('tiny_mce_before_init', function ($init) {
            $init['paste_as_text'] = true;
            return $init;
        });
    });
    
    /**
     * Edit admin menu
     */
    add_action('admin_menu', function () {
        remove_menu_page('edit-comments.php');
    });
    
    /**
     * Edit admin bar
     */
    add_action('wp_before_admin_bar_render', function () {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    });
    
    /**
     * No Gutenberg for posts
     */
    add_filter('use_block_editor_for_post_type', function ($use_block_editor, $post_type) {
        if ($post_type === 'post') {
            return false;
        }
        
        return $use_block_editor;
    }, 10, 2);
    
    /**
     * Index status in admin bar
     */
    add_action('admin_bar_menu', function ($bar) {
        if (!current_user_can('manage_options')) return;
        
        $noindex = get_option('blog_public') == '0';
        
        $bar->add_node([
            'id'    => 'index-status',
            'title' => sprintf(
                '<span style="background:%s;color:#fff;padding:2px 7px;border-radius:4px;font-size:11px;font-weight:600;">%s</span>',
                $noindex ? '#dc3232' : '#46b450',
                $noindex ? 'No index' : 'Index'
            ),
        ]);
    }, 100);
    
    /**
     * Post archive pagination
     */
    add_filter('query_vars', function ($vars) {
        $vars[] = 'postpaged';
        $vars[] = 'archive_cat';
        return $vars;
    });
    
    /**
     * Disable notification emails
     */
    add_filter('auto_theme_update_send_email', '__return_false');
    add_filter('auto_core_update_send_email', '__return_false');
    add_filter('auto_plugin_update_send_email', '__return_false');

