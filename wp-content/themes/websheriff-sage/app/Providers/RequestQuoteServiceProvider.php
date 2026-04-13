<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class RequestQuoteServiceProvider extends SageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        add_action('wp_ajax_send_quote_email', [$this, 'sendQuoteEmail']);
        add_action('wp_ajax_nopriv_send_quote_email', [$this, 'sendQuoteEmail']);
        add_action('wp_ajax_get_sterkado_products', [$this, 'getSterkadoProducts']);
        add_action('wp_ajax_nopriv_get_sterkado_products', [$this, 'getSterkadoProducts']);
        add_action('wp_ajax_get_sterkado_shipping_costs', [$this, 'getSterkadoShippingCosts']);
        add_action('wp_ajax_nopriv_get_sterkado_shipping_costs', [$this, 'getSterkadoShippingCosts']);

        add_action('wp_enqueue_scripts', [$this, 'localizeRequestQuote'], 5);
    }

    public function localizeRequestQuote(): void
    {
        if (! $this->shouldExposeRequestQuoteConfig()) {
            return;
        }

        $imgBase = get_template_directory_uri() . '/resources/images/request-quote/';

        $handle = 'sage-request-quote-config';
        wp_register_script($handle, false, [], false, true);
        wp_enqueue_script($handle);
        wp_localize_script($handle, 'RequestQuoteAjax', [
            'ajax_url'           => admin_url('admin-ajax.php'),
            'nonce'              => wp_create_nonce('ws_send_quote_email'),
            'redirectQuote'      => apply_filters('sage/request_quote_redirect_quote', home_url('/bedankt-bestel-offerte')),
            'redirectOrder'      => apply_filters('sage/request_quote_redirect_order', home_url('/bedankt-bestel-bestelling')),
            'placeholderImage'   => $imgBase . 'placeholder.svg',
            'shippingImages'     => [
                'locatie'      => $imgBase . 'een-locatie.jpg',
                'locaties'     => $imgBase . 'meer-locaties.jpg',
                'huisadressen' => $imgBase . 'huisadressen.jpg',
                'email'        => $imgBase . 'email.jpg',
            ],
        ]);
    }

    protected function shouldExposeRequestQuoteConfig(): bool
    {
        if (is_admin()) {
            return false;
        }

        if (! is_singular()) {
            return false;
        }

        $post = get_queried_object();

        return $post instanceof \WP_Post && has_block('acf/request-quote', $post);
    }

    public function sendQuoteEmail(): void
    {
        check_ajax_referer('ws_send_quote_email', '_ajax_nonce');

        $raw = isset($_POST['state']) ? wp_unslash($_POST['state']) : '';
        $data = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            wp_send_json_error('Invalid payload', 400);
        }

        $sanitize = function ($value) use (&$sanitize) {
            if (is_array($value)) {
                return array_map($sanitize, $value);
            }
            if (is_scalar($value)) {
                return sanitize_text_field((string) $value);
            }

            return '';
        };
        $state = $sanitize($data);

        $user = $state['userData'] ?? [];
        $first = $user['firstName'] ?? '';
        $last = $user['lastName'] ?? '';
        $company = $user['companyName'] ?? '';
        $phone = $user['phone'] ?? '';
        $email = $user['email'] ?? '';
        $notes = $user['notes'] ?? '';

        $fields = [
            'Aantal medewerkers'    => $state['employeeAmount'] ?? '',
            'Budget per medewerker' => $state['employeeBudget'] ?? '',
            'Product'               => trim(($state['productName'] ?? '') . ' (' . ($state['productKey'] ?? '') . ')'),
            'Variant'               => trim(($state['variantName'] ?? '') . ' (' . ($state['variantKey'] ?? '') . ')'),
            'Extra'                 => trim(($state['extraName'] ?? '') . ' (' . ($state['extraKey'] ?? '') . ')'),
            'Verzending'            => $state['shipping'] ?? '',
            'Locaties'              => is_array($state['shippingLocations'] ?? null)
                ? implode(', ', $state['shippingLocations'])
                : ($state['shippingLocations'] ?? ''),
        ];

        $title = 'Nieuwe offerte-aanvraag via website';
        $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);

        ob_start();
        ?>
        <html>
        <body>
        <h2><?php echo esc_html($title); ?></h2>

        <h3>Contactgegevens</h3>
        <table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;">
            <tr><th align="left">Naam:</th><td><?php echo esc_html(trim($first . ' ' . $last)); ?></td></tr>
            <tr><th align="left">Bedrijf:</th><td><?php echo esc_html($company); ?></td></tr>
            <tr><th align="left">Telefoon:</th><td><?php echo esc_html($phone); ?></td></tr>
            <tr><th align="left">E-mail:</th><td><?php echo esc_html($email); ?></td></tr>
            <tr><th align="left">Notities:</th><td><?php echo nl2br(esc_html($notes)); ?></td></tr>
        </table>

        <h3>Gemaakte keuzes</h3>
        <table cellpadding="6" cellspacing="0" border="1" style="border-collapse:collapse;">
            <tr><th align="left">Type:</th><td><?php echo nl2br(esc_html((string) ($_POST['cta_type'] ?? ''))); ?></td></tr>
            <?php foreach ($fields as $label => $value) : ?>
            <tr>
                <th align="left"><?php echo esc_html($label); ?>:</th>
                <td><?php echo nl2br(esc_html((string) $value)); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <p style="color:#888;margin-top:16px;"><?php echo esc_html($site); ?> – automatische melding</p>
        </body>
        </html>
        <?php
        $message = (string) ob_get_clean();

        $ctaType = isset($_POST['cta_type']) ? sanitize_text_field(wp_unslash((string) $_POST['cta_type'])) : '';
        if ($ctaType === 'Bestelling') {
            $subject = 'Bestelfow - Bestelling aangevraagd';
        } elseif ($ctaType === 'Offerte') {
            $subject = 'Bestelfow - Offerte aangevraagd';
        } else {
            $subject = sprintf('[%s] Offerte-aanvraag', $site);
        }

        $recipients = apply_filters('sage/request_quote_mail_recipients', [
            'sales@sterkado.nl',
            'jurrevanderhaven@sterkado.nl',
            'lottevankessel@sterkado.nl',
            'hansopmeer@sterkado.nl',
        ]);
        if (! is_array($recipients) || $recipients === []) {
            wp_send_json_error('No recipients', 500);
        }
        $to = implode(',', array_map('sanitize_email', $recipients));

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $domain = wp_parse_url(home_url(), PHP_URL_HOST);
        if (is_string($domain) && $domain !== '') {
            $headers[] = 'From: noreply@' . $domain;
        }
        if (! empty($email) && is_email($email)) {
            $headers[] = 'Reply-To: ' . $email;
        }

        $sent = wp_mail($to, $subject, $message, $headers);

        if ($sent) {
            wp_send_json_success();
        }

        wp_send_json_error('Mail send failed', 500);
    }

    public function getSterkadoProducts(): void
    {
        if (! defined('DOING_AJAX') || ! DOING_AJAX) {
            wp_die('Unauthorized', '', ['response' => 403]);
        }

        $quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 0;
        $budget = isset($_GET['budget']) ? (int) $_GET['budget'] : 0;

        if ($quantity <= 0 || $budget <= 0) {
            wp_send_json_error([
                'message'  => 'Aantal medewerkers en budget zijn verplicht.',
                'quantity' => $quantity,
                'budget'   => $budget,
            ]);
        }

        if (! defined('STERKADO_SALT') || STERKADO_SALT === '') {
            wp_send_json_error([
                'message' => 'Serverconfiguratie ontbreekt. STERKADO_SALT is niet gedefinieerd.',
            ]);
        }

        $authHash = hash('sha256', gmdate('Ymd') . STERKADO_SALT);

        $api_url = sprintf(
            'https://portal.sterkado.nl/api/sterkado/get-products?quantity=%d&budget=%d',
            $quantity,
            $budget
        );

        $response = wp_remote_get($api_url, [
            'headers' => [
                'Authentication' => $authHash,
                'Cache-Control'  => 'no-cache',
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error([
                'message' => 'Sterkado API request failed.',
                'error'   => $response->get_error_message(),
            ]);
        }

        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            wp_send_json_error([
                'message' => 'Sterkado API returned invalid JSON.',
            ]);
        }

        wp_send_json_success($decoded);
    }

    public function getSterkadoShippingCosts(): void
    {
        if (! defined('DOING_AJAX') || ! DOING_AJAX) {
            wp_die('Unauthorized', '', ['response' => 403]);
        }

        $quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 0;
        $locations = isset($_GET['locations']) ? (int) $_GET['locations'] : 0;
        $product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

        if ($quantity <= 0 || $locations <= 0 || $product_id <= 0) {
            wp_send_json_error([
                'message'    => 'Aantal medewerkers, locatiegegevens en een product id zijn verplicht.',
                'quantity'   => $quantity,
                'locations'  => $locations,
                'product_id' => $product_id,
            ]);
        }

        if (! defined('STERKADO_SALT') || STERKADO_SALT === '') {
            wp_send_json_error([
                'message' => 'Serverconfiguratie ontbreekt. STERKADO_SALT is niet gedefinieerd.',
            ]);
        }

        $authHash = hash('sha256', gmdate('Ymd') . STERKADO_SALT);

        $api_url = sprintf(
            'https://portal.sterkado.nl/api/sterkado/get-shipping-costs?quantity=%d&locations=%d&productId=%d',
            $quantity,
            $locations,
            $product_id
        );

        $response = wp_remote_get($api_url, [
            'headers' => [
                'Authentication' => $authHash,
                'Cache-Control'  => 'no-cache',
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error([
                'message' => 'Sterkado API request failed.',
                'error'   => $response->get_error_message(),
            ]);
        }

        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            wp_send_json_error([
                'message' => 'Sterkado API returned invalid JSON.',
            ]);
        }

        wp_send_json_success($decoded);
    }
}
