@php
$query = $query ?? null;
if (!$query || !($query instanceof \WP_Query) || $query->max_num_pages <= 1) {
    return;
}
$current = (int) get_query_var('paged') ?: (int) get_query_var('page') ?: 1;
$current = max(1, $current);
@endphp

<nav class="pagination" aria-label="{{ __('Pagination', 'sage') }}">
    {!!
        paginate_links([
            'total'     => $query->max_num_pages,
            'current'   => $current,
            'prev_text' => '&laquo; ' . __('Vorige', 'sage'),
            'next_text' => __('Volgende', 'sage') . ' &raquo;',
            'type'      => 'list',
        ])
    !!}
</nav>
