<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class AcfBlockServiceProvider extends SageServiceProvider
{
    /**
     * Slug => Title
     */
    protected array $blocks = [
        'hero' => 'Hero',
        'logos' => 'Logos',
        'text' => 'Text',
        'text-images' => 'Text Images',
        'content-cards' => 'Content cards',
        'occasion-slider' => 'Occasion slider',
        'post-selection' => 'Post selection',
        'product-selection' => 'Product selection',
        'cta' => 'CTA',
        'gallery-marquee' => 'Gallery marquee',
        'card-links' => 'Card links',
        'request-quote' => 'Request quote',
        'lead-form' => 'Lead form',
        'contact' => 'Contact',
        'office-map' => 'Office map',
        'faq' => 'FAQ',
        'faq-selection' => 'FAQ selection',
        'review-selection' => 'Review selection',
        'not-found' => 'Niet gevonden',
    ];

    public function boot(): void
    {
        parent::boot();

        add_filter('block_categories_all', [$this, 'addBlockCategory'], 10, 2);
        add_action('acf/init', [$this, 'registerBlocks']);
        add_filter('allowed_block_types_all', [$this, 'allowedBlocks'], 10, 2);
    }

    public function addBlockCategory(array $categories, $post): array
    {
        foreach ($categories as $cat) {
            if (($cat['slug'] ?? null) === 'websheriff') {
                return $categories;
            }
        }

        $categories[] = [
            'slug' => 'websheriff',
            'title' => __('Websheriff blocks', 'websheriff'),
        ];

        return $categories;
    }

    public function registerBlocks(): void
    {
        if (! function_exists('acf_register_block_type')) {
            return;
        }

        foreach ($this->blocks as $name => $title) {
            acf_register_block_type([
                'name' => $name,
                'title' => __($title, 'websheriff'),
                'category' => 'websheriff',
                'icon' => 'editor-code',
                'mode' => 'auto',
                'supports' => [
                    'anchor' => true,
                ],
                'render_callback' => [$this, 'renderBlock'],
            ]);
        }
    }

    /**
     * Render callback for all ACF blocks.
     */
    public function renderBlock(array $block, string $content = '', bool $isPreview = false, int $postId = 0): void
    {
        $slug = str_replace('acf/', '', $block['name'] ?? '');
        $view = "components.blocks.{$slug}";

        $fields = $this->getBlockFields($block);

        $data = [
            'block' => $block,
            'fields' => $fields,
            'is_preview' => $isPreview,
            'post_id' => $postId,
            'slug' => $slug,
            'content' => $content,
        ];

        $data = array_merge($data, $this->prepareBlockData($slug, $fields, $postId));

        if (function_exists('view') && view()->exists($view)) {
            echo view($view, $data)->render();

            return;
        }

        echo "<!-- Missing block view: {$view} -->";
    }

    /**
     * Best-effort block field retrieval.
     */
    protected function getBlockFields(array $block): array
    {
        $blockId = $block['id'] ?? null;

        if ($blockId && function_exists('get_fields')) {
            $scoped = get_fields($blockId);
            if (is_array($scoped)) {
                return $scoped;
            }
        }

        if (function_exists('get_fields')) {
            $fallback = get_fields();

            return is_array($fallback) ? $fallback : [];
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareBlockData(string $slug, array $fields, int $postId = 0): array
    {
        if ($slug === 'post-selection') {
            return [
                'posts' => $this->getPostSelectionPosts($fields, $postId),
            ];
        }

        if ($slug === 'card-links') {
            return [
                'cards' => $this->buildCardLinksManualCards($fields),
            ];
        }

        if ($slug === 'product-selection') {
            return $this->getProductSelectionBlockData($fields);
        }

        if ($slug === 'gallery-marquee') {
            return $this->getGalleryMarqueeBlockData($fields);
        }

        if ($slug === 'faq') {
            return $this->getFaqBlockData($fields, $postId);
        }

        if ($slug === 'faq-selection') {
            return [
                'faq_selection_questions' => $this->getFaqSelectionQuestions($fields),
            ];
        }

        if ($slug === 'review-selection') {
            return [
                'reviews' => $this->getReviewSelectionReviews($fields),
            ];
        }

        if ($slug === 'contact') {
            return [
                'contact_methods' => $this->getContactBlockMethods($fields),
            ];
        }

        if ($slug === 'office-map') {
            return [
                'office_map_social_rows' => \App\social_links_from_options(),
            ];
        }

        if ($slug === 'not-found') {
            return $this->getNotFoundBlockData($fields);
        }

        return [];
    }

    /**
     * @return array{
     *     faq_term_cards: array<int, array{term: \WP_Term, posts: array<int, \WP_Post>}>,
     *     faq_search_query: string,
     *     faq_search_results: array<int, \WP_Post>,
     *     faq_card_icon_class: string,
     *     faq_form_action: string
     * }
     */
    protected function getFaqBlockData(array $fields, int $postId): array
    {
        $perTerm = isset($fields['questions_per_category']) ? (int) $fields['questions_per_category'] : 50;
        $perTerm = max(1, min(100, $perTerm));

        $terms = get_terms([
            'taxonomy' => QuestionPostTypeServiceProvider::TAXONOMY_CATEGORY,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (is_wp_error($terms) || ! is_array($terms)) {
            $terms = [];
        }

        $cards = [];
        foreach ($terms as $term) {
            if (! $term instanceof \WP_Term) {
                continue;
            }
            $posts = get_posts([
                'post_type' => QuestionPostTypeServiceProvider::POST_TYPE,
                'post_status' => 'publish',
                'posts_per_page' => $perTerm,
                'orderby' => 'title',
                'order' => 'ASC',
                'tax_query' => [
                    [
                        'taxonomy' => QuestionPostTypeServiceProvider::TAXONOMY_CATEGORY,
                        'field' => 'term_id',
                        'terms' => (int) $term->term_id,
                    ],
                ],
                'no_found_rows' => true,
            ]);
            if ($posts !== [] && function_exists('update_post_caches')) {
                update_post_caches($posts);
            }
            $cards[] = [
                'term' => $term,
                'posts' => $posts,
            ];
        }

        $searchQuery = isset($_GET['faq_s']) ? sanitize_text_field(wp_unslash((string) $_GET['faq_s'])) : '';
        $searchQuery = trim($searchQuery);

        $searchResults = [];
        if ($searchQuery !== '') {
            $searchResults = get_posts([
                'post_type' => QuestionPostTypeServiceProvider::POST_TYPE,
                'post_status' => 'publish',
                'posts_per_page' => 40,
                'orderby' => 'relevance',
                'order' => 'DESC',
                's' => $searchQuery,
                'no_found_rows' => true,
            ]);
            if ($searchResults !== [] && function_exists('update_post_caches')) {
                update_post_caches($searchResults);
            }
        }

        $actionId = $postId > 0 ? $postId : (int) get_queried_object_id();
        $formAction = $actionId > 0 ? (string) get_permalink($actionId) : home_url('/');

        return [
            'faq_term_cards' => $cards,
            'faq_search_query' => $searchQuery,
            'faq_search_results' => $searchResults,
            'faq_card_icon_class' => $this->getFaqCardIconClass(isset($fields['card_icon']) ? (string) $fields['card_icon'] : null),
            'faq_form_action' => $formAction,
        ];
    }

    protected function getFaqCardIconClass(?string $key): string
    {
        return match ($key ?? '') {
            'truck' => 'fa-solid fa-truck',
            'shield' => 'fa-solid fa-shield-halved',
            'comments' => 'fa-solid fa-comments',
            'info' => 'fa-solid fa-circle-info',
            default => 'fa-solid fa-gift',
        };
    }

    /**
     * @return array{gallery_marquee_top: array<int, array<string, mixed>>, gallery_marquee_bottom: array<int, array<string, mixed>>, gallery_marquee_top_duration: int, gallery_marquee_bottom_duration: int}
     */
    protected function getGalleryMarqueeBlockData(array $fields): array
    {
        $images = $this->normalizeGalleryField($fields['gallery'] ?? null);
        if ($images === []) {
            return [
                'gallery_marquee_top' => [],
                'gallery_marquee_bottom' => [],
                'gallery_marquee_top_duration' => 40,
                'gallery_marquee_bottom_duration' => 40,
            ];
        }

        $minStripCells = max(24, count($images) * 4);
        $topCycle = $this->shuffleGalleryImages($images);
        $bottomCycle = $this->shuffleGalleryImages($images);
        $top = $this->expandMarqueeStrip($topCycle, $minStripCells);
        $bottom = $this->expandMarqueeStrip($bottomCycle, $minStripCells);

        $topDuration = (int) max(30, min(100, count($top) * 2));
        $bottomDuration = (int) max(30, min(100, count($bottom) * 2));

        return [
            'gallery_marquee_top' => $top,
            'gallery_marquee_bottom' => $bottom,
            'gallery_marquee_top_duration' => $topDuration,
            'gallery_marquee_bottom_duration' => $bottomDuration,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $images
     * @return array<int, array<string, mixed>>
     */
    protected function shuffleGalleryImages(array $images): array
    {
        $copy = array_values($images);
        shuffle($copy);

        return $copy;
    }

    /**
     * Repeat a shuffled cycle until the strip is long enough to fill wide viewports when duplicated for CSS marquee.
     *
     * @param  array<int, array<string, mixed>>  $cycle
     * @return array<int, array<string, mixed>>
     */
    protected function expandMarqueeStrip(array $cycle, int $minItems): array
    {
        if ($cycle === []) {
            return [];
        }

        $out = [];
        while (count($out) < $minItems) {
            foreach ($cycle as $img) {
                $out[] = $img;
            }
        }

        return $out;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeGalleryField(mixed $galleryRaw): array
    {
        if (! is_array($galleryRaw)) {
            return [];
        }

        $items = array_values(array_filter($galleryRaw, static function ($item): bool {
            return is_array($item) && (! empty($item['ID']) || ! empty($item['url']));
        }));

        return $items;
    }

    /**
     * @return array{product_cards: array<int, array<string, mixed>>, product_secondary_button: array<string, string>, product_primary_label: string}
     */
    protected function getProductSelectionBlockData(array $fields): array
    {
        $posts = $this->getProductSelectionProducts($fields);

        $primaryLabelRaw = isset($fields['primary_button_label']) ? trim((string) $fields['primary_button_label']) : '';
        $primaryLabel = $primaryLabelRaw !== ''
            ? $primaryLabelRaw
            : __('Lees meer', 'sage');

        $secondaryRaw = $fields['secondary_button'] ?? null;
        $secondaryRaw = is_array($secondaryRaw) ? $secondaryRaw : [];
        $secondary = [
            'url' => isset($secondaryRaw['url']) ? trim((string) $secondaryRaw['url']) : '',
            'title' => isset($secondaryRaw['title']) ? trim((string) $secondaryRaw['title']) : '',
            'target' => isset($secondaryRaw['target']) ? (string) $secondaryRaw['target'] : '_self',
        ];

        $cards = [];
        foreach ($posts as $post) {
            if (! $post instanceof \WP_Post) {
                continue;
            }
            $short = function_exists('get_field') ? get_field('short_description', $post->ID) : null;
            $short = is_string($short) ? trim($short) : '';
            $price = function_exists('get_field') ? get_field('price', $post->ID) : null;
            $price = is_string($price) ? trim($price) : '';

            $cards[] = [
                'id' => (int) $post->ID,
                'title' => get_the_title($post),
                'permalink' => (string) get_permalink($post),
                'short_description' => $short,
                'price' => $price,
                'thumb_id' => (int) get_post_thumbnail_id($post),
            ];
        }

        return [
            'product_cards' => $cards,
            'product_secondary_button' => $secondary,
            'product_primary_label' => $primaryLabel,
        ];
    }

    /**
     * @return array<int, \WP_Post>
     */
    protected function getProductSelectionProducts(array $fields): array
    {
        $source = isset($fields['product_list_source']) ? (string) $fields['product_list_source'] : 'manual';
        if (! in_array($source, ['manual', 'category'], true)) {
            $source = 'manual';
        }

        if ($source === 'category') {
            $termId = $this->resolveAcfTaxonomyTermId($fields['product_category'] ?? null);
            if ($termId === null) {
                return [];
            }

            $max = isset($fields['max_products']) ? (int) $fields['max_products'] : 12;
            $max = max(1, min(48, $max));

            $posts = get_posts([
                'post_type' => ProductPostTypeServiceProvider::POST_TYPE,
                'post_status' => 'publish',
                'posts_per_page' => $max,
                'orderby' => 'date',
                'order' => 'DESC',
                'no_found_rows' => true,
                'tax_query' => [
                    [
                        'taxonomy' => ProductPostTypeServiceProvider::TAXONOMY_CATEGORY,
                        'field' => 'term_id',
                        'terms' => [$termId],
                    ],
                ],
            ]);

            if ($posts !== [] && function_exists('update_post_caches')) {
                update_post_caches($posts);
            }

            return $posts;
        }

        $selected = $fields['products'] ?? null;
        if (! is_array($selected) || $selected === []) {
            return [];
        }

        $ids = [];
        foreach ($selected as $item) {
            if ($item instanceof \WP_Post) {
                $ids[] = (int) $item->ID;
            } elseif (is_numeric($item)) {
                $ids[] = (int) $item;
            }
        }
        $ids = array_values(array_unique(array_filter($ids)));

        if ($ids === []) {
            return [];
        }

        $posts = get_posts([
            'post_type' => ProductPostTypeServiceProvider::POST_TYPE,
            'post__in' => $ids,
            'posts_per_page' => -1,
            'orderby' => 'post__in',
            'post_status' => 'publish',
        ]);

        if ($posts !== [] && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }

        return $posts;
    }

    /**
     * @return array<int, array{name: string, description: string, url: string, button_label: string, button_style: string|null}>
     */
    protected function buildCardLinksManualCards(array $fields): array
    {
        $manual = $fields['manual_cards'] ?? null;
        if (! is_array($manual) || $manual === []) {
            return [];
        }

        $globalBtnRaw = isset($fields['button_label']) ? trim((string) $fields['button_label']) : '';
        $globalBtn = $globalBtnRaw !== ''
            ? $globalBtnRaw
            : __('Meer info', 'sage');

        $out = [];
        foreach ($manual as $row) {
            if (! is_array($row)) {
                continue;
            }
            $link = $row['card_link'] ?? null;
            $link = is_array($link) ? $link : [];
            $url = isset($link['url']) ? trim((string) $link['url']) : '';
            if ($url === '') {
                continue;
            }
            $title = isset($row['card_title']) ? trim((string) $row['card_title']) : '';
            $textRaw = $row['card_text'] ?? '';
            $description = is_string($textRaw) ? trim($textRaw) : '';
            $rowBtn = isset($row['card_button_label']) ? trim((string) $row['card_button_label']) : '';
            $buttonLabel = $rowBtn !== '' ? $rowBtn : $globalBtn;
            $styleRaw = isset($row['button_style']) ? trim((string) $row['button_style']) : '';
            $buttonStyle = in_array($styleRaw, ['primary', 'secondary', 'tertiary'], true) ? $styleRaw : null;
            $out[] = [
                'name' => $title,
                'description' => $description,
                'url' => $url,
                'button_label' => $buttonLabel,
                'button_style' => $buttonStyle,
            ];
        }

        return $out;
    }

    /**
     * @return array<int, \WP_Post>
     */
    protected function getPostSelectionPosts(array $fields, int $contextPostId = 0): array
    {
        $source = isset($fields['post_list_source']) ? (string) $fields['post_list_source'] : 'recent';
        if (! in_array($source, ['category', 'recent'], true)) {
            $source = 'recent';
        }

        $max = isset($fields['max_posts']) ? (int) $fields['max_posts'] : 8;
        $max = max(1, min(48, $max));

        $exclude = [];
        if ($contextPostId > 0) {
            $exclude[] = $contextPostId;
        }

        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $max,
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
        ];

        if ($exclude !== []) {
            $args['post__not_in'] = $exclude;
        }

        if ($source === 'category') {
            $termId = $this->resolveAcfTaxonomyTermId($fields['post_category'] ?? null);
            if ($termId === null) {
                return [];
            }
            $args['tax_query'] = [
                [
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => [$termId],
                ],
            ];
        }

        $posts = get_posts($args);

        if ($posts !== [] && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }

        return $posts;
    }

    /**
     * @param  mixed  $raw  ACF taxonomy (return ID): int, numeric string, WP_Term, or empty.
     */
    protected function resolveAcfTaxonomyTermId(mixed $raw): ?int
    {
        if ($raw instanceof \WP_Term) {
            return (int) $raw->term_id;
        }
        if (is_array($raw)) {
            $first = $raw[0] ?? null;
            if (! is_numeric($first)) {
                return null;
            }
            $id = (int) $first;

            return $id > 0 ? $id : null;
        }
        if (is_numeric($raw)) {
            $id = (int) $raw;

            return $id > 0 ? $id : null;
        }

        return null;
    }

    /**
     * @return array<int, \WP_Post>
     */
    protected function getReviewSelectionReviews(array $fields): array
    {
        $selected = $fields['reviews'] ?? null;
        if (! is_array($selected) || $selected === []) {
            return [];
        }

        $ids = [];
        foreach ($selected as $item) {
            if ($item instanceof \WP_Post) {
                $ids[] = (int) $item->ID;
            } elseif (is_numeric($item)) {
                $ids[] = (int) $item;
            }
        }
        $ids = array_values(array_unique(array_filter($ids)));

        if ($ids === []) {
            return [];
        }

        $posts = get_posts([
            'post_type' => ReviewPostTypeServiceProvider::POST_TYPE,
            'post__in' => $ids,
            'posts_per_page' => -1,
            'orderby' => 'post__in',
            'post_status' => 'publish',
        ]);

        if ($posts !== [] && function_exists('update_post_caches')) {
            update_post_caches($posts);
        }

        return $posts;
    }

    /**
     * Questions from the selected question categories, in category order, without duplicates.
     *
     * @return array<int, \WP_Post>
     */
    protected function getFaqSelectionQuestions(array $fields): array
    {
        $raw = $fields['question_categories'] ?? null;
        if (! is_array($raw)) {
            $raw = ($raw !== null && $raw !== '') ? [$raw] : [];
        }
        $termIds = [];
        foreach ($raw as $item) {
            if ($item instanceof \WP_Term) {
                $termIds[] = (int) $item->term_id;
            } elseif (is_numeric($item)) {
                $termIds[] = (int) $item;
            }
        }
        $termIds = array_values(array_unique(array_filter($termIds)));

        if ($termIds === []) {
            return [];
        }

        $seen = [];
        $ordered = [];
        foreach ($termIds as $termId) {
            if ($termId <= 0) {
                continue;
            }
            $posts = get_posts([
                'post_type' => QuestionPostTypeServiceProvider::POST_TYPE,
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC',
                'tax_query' => [
                    [
                        'taxonomy' => QuestionPostTypeServiceProvider::TAXONOMY_CATEGORY,
                        'field' => 'term_id',
                        'terms' => $termId,
                    ],
                ],
                'no_found_rows' => true,
            ]);
            foreach ($posts as $post) {
                if (! $post instanceof \WP_Post) {
                    continue;
                }
                $id = (int) $post->ID;
                if (isset($seen[$id])) {
                    continue;
                }
                $seen[$id] = true;
                $ordered[] = $post;
            }
        }

        if ($ordered !== [] && function_exists('update_post_caches')) {
            update_post_caches($ordered);
        }

        return $ordered;
    }

    /**
     * @return list<array{icon_class: string, line_primary: string, line_secondary: string, link: array{url: string, title: string, target: string}}>
     */
    protected function getContactBlockMethods(array $fields): array
    {
        $raw = $fields['contact_methods'] ?? null;
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $row) {
            if (! is_array($row)) {
                continue;
            }
            $iconKey = isset($row['icon']) ? (string) $row['icon'] : '';
            $linePrimary = isset($row['line_primary']) ? trim((string) $row['line_primary']) : '';
            $lineSecondary = isset($row['line_secondary']) ? trim((string) $row['line_secondary']) : '';
            $link = $row['link'] ?? null;
            $link = is_array($link) ? $link : [];
            $linkUrl = isset($link['url']) ? trim((string) $link['url']) : '';
            $linkTitle = isset($link['title']) ? trim((string) $link['title']) : '';
            $linkTarget = isset($link['target']) ? (string) $link['target'] : '_self';

            if ($linePrimary === '' && $lineSecondary === '' && $linkUrl === '') {
                continue;
            }

            $out[] = [
                'icon_class' => $this->contactMethodFaClass($iconKey),
                'line_primary' => $linePrimary,
                'line_secondary' => $lineSecondary,
                'link' => [
                    'url' => $linkUrl,
                    'title' => $linkTitle,
                    'target' => $linkTarget !== '' ? $linkTarget : '_self',
                ],
            ];
        }

        return $out;
    }

    /**
     * @return array{
     *     not_found_phone_primary: string,
     *     not_found_phone_secondary: string,
     *     not_found_phone_url: string,
     *     not_found_email_primary: string,
     *     not_found_email_secondary: string,
     *     not_found_email_url: string
     * }
     */
    protected function getNotFoundBlockData(array $fields): array
    {
        $defaultPhoneHint = __('Op werkdagen van 9.00-17.00 uur', 'websheriff');
        $defaultEmailHint = __('Binnen een werkdag een reactie', 'websheriff');

        $phoneHint = isset($fields['phone_hint']) ? trim((string) $fields['phone_hint']) : '';
        $emailHint = isset($fields['email_hint']) ? trim((string) $fields['email_hint']) : '';
        if ($phoneHint === '') {
            $phoneHint = $defaultPhoneHint;
        }
        if ($emailHint === '') {
            $emailHint = $defaultEmailHint;
        }

        $phone = '';
        $email = '';
        if (function_exists('get_field')) {
            $phone = trim((string) (get_field('phone', 'option') ?: ''));
            $email = trim((string) (get_field('email', 'option') ?: ''));
        }

        $telHref = '';
        if ($phone !== '') {
            $digits = preg_replace('/\D+/', '', $phone) ?? '';
            $telHref = $digits !== '' ? 'tel:'.$digits : '';
        }

        $sanitizedEmail = sanitize_email($email);
        $mailto = ($email !== '' && $sanitizedEmail !== '') ? 'mailto:'.$sanitizedEmail : '';

        return [
            'not_found_phone_primary' => $phone !== '' ? sprintf(
                /* translators: %s: phone number as stored in site options */
                __('Tel: %s', 'websheriff'),
                $phone
            ) : '',
            'not_found_phone_secondary' => $phone !== '' ? $phoneHint : '',
            'not_found_phone_url' => $telHref,
            'not_found_email_primary' => $email !== '' ? sprintf(
                /* translators: %s: email address as stored in site options */
                __('E-mail: %s', 'websheriff'),
                $email
            ) : '',
            'not_found_email_secondary' => $email !== '' ? $emailHint : '',
            'not_found_email_url' => $mailto,
        ];
    }

    protected function contactMethodFaClass(string $key): string
    {
        return match ($key) {
            'phone' => 'fa-solid fa-phone',
            'mobile' => 'fa-solid fa-mobile-screen',
            'envelope' => 'fa-solid fa-envelope',
            'location' => 'fa-solid fa-location-dot',
            'clock' => 'fa-solid fa-clock',
            'user' => 'fa-solid fa-user',
            'message' => 'fa-solid fa-comments',
            'whatsapp' => 'fa-brands fa-whatsapp',
            'info' => 'fa-solid fa-circle-info',
            default => 'fa-solid fa-circle-info',
        };
    }

    public function allowedBlocks($allowed, $context): array
    {
        $postType = $context->post->post_type ?? null;

        if (in_array($postType, ['post', 'product', 'question', 'review'], true)) {
            return [
                'core/paragraph',
                'core/heading',
                'core/list',
                'core/list-item',
                'core/image',
            ];
        }

        $acfBlocks = array_map(fn ($slug) => "acf/{$slug}", array_keys($this->blocks));

        return array_values(array_unique($acfBlocks));
    }
}
