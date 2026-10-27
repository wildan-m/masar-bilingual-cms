<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Display time zone
    |--------------------------------------------------------------------------
    |
    | Timestamps are stored in UTC and shown to staff and visitors in the
    | firm's local time.
    |
    */

    'display_timezone' => env('APP_DISPLAY_TIMEZONE', 'Asia/Dubai'),

    /*
    |--------------------------------------------------------------------------
    | Requests for proposal
    |--------------------------------------------------------------------------
    |
    | Attachments are written to a private disk under random file names and
    | are only ever streamed through an authorised controller. Use a private
    | S3 bucket (or similar) in production by pointing the disk at it.
    |
    */

    'rfp' => [
        'attachment_disk' => env('RFP_ATTACHMENT_DISK', 'private'),
        'attachment_directory' => 'proposal-requests',
        'attachment_extensions' => ['pdf', 'docx', 'xlsx'],
        'attachment_max_kilobytes' => 10 * 1024,
        'rate_limit_per_minute' => (int) env('RFP_RATE_LIMIT_PER_MINUTE', 5),
        'signed_link_ttl_days' => 14,
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity log
    |--------------------------------------------------------------------------
    */

    'activity_log' => [
        'retention_months' => 24,
    ],

];
