<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Kitchen Queue Limit
    |--------------------------------------------------------------------------
    | Batas maksimal pesanan dalam status 'processing' di dapur secara bersamaan.
    */
    'max_kitchen_queue' => (int) env('MAX_KITCHEN_QUEUE', 5),
];
