<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HrdController extends Controller
{
    public function organization()
    {
         // Dummy hierarchical data
        $positions = [
            [
                'id' => 1,
                'title' => 'CEO',
                'name' => 'Andi',
                'children' => [
                    [
                        'id' => 2,
                        'title' => 'HR Manager',
                        'name' => 'Siti',
                        'children' => [
                            ['id' => 3, 'title' => 'Recruitment Officer', 'name' => null, 'children' => []],
                        ],
                    ],
                    [
                        'id' => 4,
                        'title' => 'IT Manager',
                        'name' => 'Budi',
                        'children' => [
                            ['id' => 5, 'title' => 'Developer', 'name' => null, 'children' => []],
                        ],
                    ],
                ],
            ]
        ];

        return view('hr.organization', compact('positions'));
    }

     public function employee()
    {
        return view('hr.employee');
    }

     public function employee_create()
    {
        return view('hr.create-employee');
    }
}
