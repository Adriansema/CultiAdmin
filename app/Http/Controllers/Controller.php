<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function index()
{
    $visits = [
        ['count' => 3, 'date' => '2025-01-01'],
        ['count' => 7, 'date' => '2025-02-01'],
        ['count' => 5, 'date' => '2025-03-01'],
        ['count' => 9, 'date' => '2025-04-01'],
        ['count' => 6, 'date' => '2025-05-01'],
    ];

    $registrations = [
        ['count' => 1, 'date' => '2025-01-01'],
        ['count' => 4, 'date' => '2025-02-01'],
        ['count' => 3, 'date' => '2025-03-01'],
        ['count' => 6, 'date' => '2025-04-01'],
        ['count' => 2, 'date' => '2025-05-01'],
    ];

    return response()->json([
        'visits' => $visits,
        'registrations' => $registrations
    ]);
}

}


