<?php

return [

    /*
    | Whether a database has been configured. The starter deploys without one;
    | starter:migrate skips until DB_CONNECTION is set.
    */
    'database' => env('DB_CONNECTION') !== null,

];
