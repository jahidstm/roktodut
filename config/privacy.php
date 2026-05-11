<?php

return [
    /*
    |--------------------------------------------------------------------------
    | NID Retention Window (days)
    |--------------------------------------------------------------------------
    |
    | Private NID data will be purged automatically after this many days
    | from latest submission. Minimum enforced value is 30 days.
    |
    */
    'nid_retention_days' => env('NID_RETENTION_DAYS', 365),
];
