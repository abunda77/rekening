<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Polling Interval
    |--------------------------------------------------------------------------
    |
    | The interval in seconds at which the notification bell component will
    | poll for new notifications. A lower value means more frequent updates
    | but more server load. Recommended: 5-10 seconds.
    |
    */

    'polling_interval' => (int) env('NOTIFICATION_POLLING_INTERVAL', 5),

    /*
    |--------------------------------------------------------------------------
    | Retention Period
    |--------------------------------------------------------------------------
    |
    | The number of months to keep old notifications before they are
    | automatically cleaned up by the notifications:cleanup command.
    | Set to 0 to disable automatic cleanup.
    |
    */

    'retention_months' => (int) env('NOTIFICATION_RETENTION_MONTHS', 3),

    /*
    |--------------------------------------------------------------------------
    | Maximum Dropdown Items
    |--------------------------------------------------------------------------
    |
    | The maximum number of notifications to show in the dropdown panel.
    | Users can view all notifications on a dedicated page if needed.
    |
    */

    'dropdown_limit' => 10,

];
