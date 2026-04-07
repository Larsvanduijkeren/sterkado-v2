<?php

namespace App\View\Composers;

use function App\last_section_footer_modifiers;
use Roots\Acorn\View\Composer;

class Footer extends Composer
{
    /**
     * @var array<int, string>
     */
    protected static $views = [
        'sections.footer',
    ];

    /**
     * Classes reflecting the last section’s background / waves (singular content only).
     */
    public function footerAfterSectionClass(): string
    {
        if (! is_singular()) {
            return '';
        }

        $post = get_queried_object();
        if (! $post instanceof \WP_Post) {
            return '';
        }

        return last_section_footer_modifiers($post);
    }
}
