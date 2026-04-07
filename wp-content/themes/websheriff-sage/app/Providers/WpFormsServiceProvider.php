<?php

namespace App\Providers;

use Roots\Acorn\Sage\SageServiceProvider;

class WpFormsServiceProvider extends SageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        add_filter('wpforms_frontend_strings', [$this, 'flatpickrDutchLocale'], 20);

        add_filter('wpforms_frontend_form_data', [$this, 'forceRecaptchaEnabledOnForm'], 5);
        add_filter('wpforms_process_before_form_data', [$this, 'forceRecaptchaEnabledOnFormProcess'], 5, 2);
        add_filter('wpforms_setting', [$this, 'forceRecaptchaV2CheckboxType'], 10, 4);
    }

    /**
     * Enable the per-form reCAPTCHA flag for every form (matches “Use CAPTCHA” in the builder).
     * Display path: {@see wpforms_frontend_form_data}.
     *
     * @param  array<string, mixed>  $form_data
     * @return array<string, mixed>
     */
    public function forceRecaptchaEnabledOnForm(array $form_data): array
    {
        if (! isset($form_data['settings']) || ! is_array($form_data['settings'])) {
            $form_data['settings'] = [];
        }
        $form_data['settings']['recaptcha'] = '1';

        return $form_data;
    }

    /**
     * Same as {@see forceRecaptchaEnabledOnForm} for submissions (AJAX/non-AJAX), so validation runs.
     *
     * @param  array<string, mixed>  $form_data
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    public function forceRecaptchaEnabledOnFormProcess(array $form_data, array $entry): array
    {
        return $this->forceRecaptchaEnabledOnForm($form_data);
    }

    /**
     * Force Google reCAPTCHA v2 (“I’m not a robot” checkbox), not invisible or v3.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function forceRecaptchaV2CheckboxType($value, string $key, $default_value, string $option)
    {
        if ($key === 'recaptcha-type') {
            return 'v2';
        }

        return $value;
    }

    /**
     * WPForms loads Flatpickr core only (no l10n/*.js). It sets `properties.locale` from
     * `wpforms_settings.locale` (e.g. "nl"), but `flatpickr.l10ns.nl` is undefined, so the calendar
     * stays English. Supply a full locale object so each date field gets Dutch labels and Monday
     * as the first day.
     *
     * @param  array<string, mixed>  $strings
     * @return array<string, mixed>
     */
    public function flatpickrDutchLocale(array $strings): array
    {
        if (! class_exists('WPForms', false)) {
            return $strings;
        }

        $strings['locale'] = [
            'weekdays' => [
                'shorthand' => ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za'],
                'longhand' => ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'],
            ],
            'months' => [
                'shorthand' => ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
                'longhand' => ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'],
            ],
            'firstDayOfWeek' => 1,
        ];

        return $strings;
    }
}
