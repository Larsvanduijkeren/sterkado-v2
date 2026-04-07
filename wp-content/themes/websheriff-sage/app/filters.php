<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

add_filter('the_content', function ($content) {
    if (empty($content)) return $content;

    // Remove <p> tags around iframes
    $content = preg_replace('/<p>\s*(<iframe.*?<\/iframe>)\s*<\/p>/i', '$1', $content);

    // Wrap iframe in a div
    $content = preg_replace_callback(
        '/(<iframe.*?<\/iframe>)/is',
        function ($matches) {
            return '<div class="iframe-wrapper">' . $matches[1] . '</div>';
        },
        $content
    );

    return $content;
});
