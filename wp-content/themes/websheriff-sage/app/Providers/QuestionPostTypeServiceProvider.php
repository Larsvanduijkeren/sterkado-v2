<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class QuestionPostTypeServiceProvider extends SageServiceProvider
{
    public const POST_TYPE = 'question';

    public const TAXONOMY_CATEGORY = 'question_category';

    public function register(): void
    {
        parent::register();
    }

    public function boot(): void
    {
        parent::boot();

        add_action('init', [$this, 'registerQuestionPostType']);
        add_action('init', [$this, 'registerQuestionCategoryTaxonomy']);
        add_filter('use_block_editor_for_post_type', [$this, 'disableBlockEditorForQuestion'], 10, 2);
        add_action('admin_post_nopriv_question_feedback', [$this, 'handleQuestionFeedback']);
        add_action('admin_post_question_feedback', [$this, 'handleQuestionFeedback']);
    }

    public function handleQuestionFeedback(): void
    {
        if (! isset($_POST['question_feedback_nonce'], $_POST['question_id'])) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        if (! wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['question_feedback_nonce'])), 'question_feedback')) {
            wp_die(__('Ongeldige aanvraag.', 'sage'), '', ['response' => 403]);
        }

        $questionId = absint($_POST['question_id']);
        if ($questionId === 0 || get_post_type($questionId) !== self::POST_TYPE) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        if (! empty($_POST['company'])) {
            wp_safe_redirect(add_query_arg('feedback', 'sent', get_permalink($questionId)));
            exit;
        }

        $first = isset($_POST['first_name']) ? sanitize_text_field(wp_unslash((string) $_POST['first_name'])) : '';
        $last = isset($_POST['last_name']) ? sanitize_text_field(wp_unslash((string) $_POST['last_name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash((string) $_POST['email'])) : '';
        $reference = isset($_POST['reference']) ? sanitize_text_field(wp_unslash((string) $_POST['reference'])) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash((string) $_POST['message'])) : '';

        if ($first === '' || $last === '' || $message === '' || ! is_email($email)) {
            wp_safe_redirect(add_query_arg('feedback', 'error', get_permalink($questionId)));
            exit;
        }

        $questionTitle = get_the_title($questionId);
        $questionUrl = get_permalink($questionId);
        $to = (string) get_option('admin_email');
        $subject = sprintf(
            /* translators: %s: site name */
            __('[%s] Vraag: hulp nodig na antwoord', 'sage'),
            wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES)
        );

        $bodyLines = [
            sprintf(/* translators: %s: question title */ __('Vraag: %s', 'sage'), $questionTitle),
            sprintf(/* translators: %s: URL */ __('URL: %s', 'sage'), $questionUrl),
            '',
            sprintf(/* translators: %s: first name */ __('Voornaam: %s', 'sage'), $first),
            sprintf(/* translators: %s: last name */ __('Achternaam: %s', 'sage'), $last),
            sprintf(/* translators: %s: email */ __('E-mail: %s', 'sage'), $email),
            sprintf(/* translators: %s: reference */ __('Barcode / bestelnummer: %s', 'sage'), $reference),
            '',
            __('Bericht:', 'sage'),
            $message,
        ];
        $body = implode("\n", $bodyLines);

        wp_mail($to, $subject, $body, [
            sprintf('Reply-To: %s', $email),
        ]);

        wp_safe_redirect(add_query_arg('feedback', 'sent', $questionUrl));
        exit;
    }

    public function disableBlockEditorForQuestion(bool $use, string $postType): bool
    {
        if ($postType === self::POST_TYPE) {
            return false;
        }

        return $use;
    }

    public function registerQuestionPostType(): void
    {
        $labels = [
            'name' => __('Vragen', 'sage'),
            'singular_name' => __('Vraag', 'sage'),
            'add_new' => __('Nieuwe vraag', 'sage'),
            'add_new_item' => __('Vraag toevoegen', 'sage'),
            'edit_item' => __('Vraag bewerken', 'sage'),
            'new_item' => __('Nieuwe vraag', 'sage'),
            'view_item' => __('Vraag bekijken', 'sage'),
            'search_items' => __('Vragen zoeken', 'sage'),
            'not_found' => __('Geen vragen gevonden', 'sage'),
            'not_found_in_trash' => __('Geen vragen in prullenbak', 'sage'),
            'menu_name' => __('Vragen', 'sage'),
        ];

        register_post_type(self::POST_TYPE, [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'hierarchical' => false,
            'menu_position' => 23,
            'menu_icon' => 'dashicons-editor-help',
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'rewrite' => ['slug' => 'vraag', 'with_front' => false],
        ]);
    }

    public function registerQuestionCategoryTaxonomy(): void
    {
        $labels = [
            'name' => __('Vraagcategorieën', 'sage'),
            'singular_name' => __('Vraagcategorie', 'sage'),
            'search_items' => __('Categorieën zoeken', 'sage'),
            'all_items' => __('Alle categorieën', 'sage'),
            'parent_item' => __('Bovenliggende categorie', 'sage'),
            'parent_item_colon' => __('Bovenliggende categorie:', 'sage'),
            'edit_item' => __('Categorie bewerken', 'sage'),
            'update_item' => __('Categorie bijwerken', 'sage'),
            'add_new_item' => __('Nieuwe categorie', 'sage'),
            'new_item_name' => __('Nieuwe categorienaam', 'sage'),
            'menu_name' => __('Categorieën', 'sage'),
        ];

        register_taxonomy(self::TAXONOMY_CATEGORY, [self::POST_TYPE], [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'vraag-categorie', 'with_front' => false],
        ]);
    }
}
