<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\WorkCase;
use Auth;

class CasesController extends Controller
{
    use LinksTrait;
    public function index()
    {

        $next = '';
        $prefix = 'Open Cases';
        $allcases_query = Auth::user()->team->cases();
        if (request('next')) {
            if (request('next') == 'held') {
                $allcases_query->held();
                $prefix = 'Held Cases';
                $next = 'app/cases?next=resolved';
            }
            if (request('next') == 'resolved') {
                $prefix = 'Resolved Cases';
                $allcases_query->resolved();
            }
        } else {
            $allcases_query->open();
            if (Auth::user()->cases()->held()->count() > 0) {
                $next = 'app/cases?next=held';
            } else {
                $next = 'app/cases?next=resolved';
            }
            
        }
        $allcases = $allcases_query->orderByDesc('created_at')->get();

        if (request('auth_test') && request('debug')) {
            print_r($allcases);   
        }
    	
        $grouped = $allcases->groupBy('last_activity_month');

        $sections = [];

        $sections[] = [
            'title'    => '',
            'subtitle' => 'Highlighted cases are assigned to you.',
            'rows'     => [],
        ];

        if (request('auth_test') && request('debug')) {
            print_r($sections);   
        }

        $rows = [];

        foreach ($grouped as $groupname => $cases) {
            $links = $this->getCasesLinks($cases);

            $rows[] = ['title' => $groupname,
                       'text'  => '',
                       'links' => $links];
        }

        $sections[] = [
                'title' => $allcases_query->count().' '.$prefix,
                'subtitle' => 'Cases are grouped by last activity month.',
                'rows' => $rows,
            ];

        $data = [
            'header'   => 'Cases',
            'next_url' => $next,
            'sections' => $sections,
        ];

        if (request('auth_test') && request('debug')) {
            print_r($data);   
        }

        return json_encode($data);
    }
    public function show($id)
    {
        //dd($id);
        $case = WorkCase::find($id);

        
        $people_obj = $case->people()->get()->sortBy('first_name');
        $people = $this->getPersonLinks($people_obj);

        $files_obj = $case->files()->get()->sortBy('created_at');
        $files = $this->getFilesLinks($files_obj);

        $notes_obj = $case->contacts()->get()->sortBy('created_at');
        $notes_rows = $this->getNotesRows($notes_obj, 'date_readable');

        $data = [
            'header' => ''.$case->name,
            'next_url' => '',
            'sections' => [
                0 => [
                    'title'    => ''.$case->name,
                    'subtitle' => ''.$case->type,
                    'rows'     => [
                        0 => [
                            'title'  => 'Assigned to',
                            'text'   => ''.$case->assigned_to_user,
                            'links'  => [],
                        ],
                        1 => [
                            'title'  => 'Description',
                            'text'   => ''.$case->notes,
                            'links'  => [],
                        ],
                        2 => [
                            'title'  => 'People',
                            'text'   => '',
                            'links'  => $people,
                        ],
                        3 => [
                            'title'  => 'Files',
                            'text'   => '',
                            'links'  => $files,
                        ],
                    ]
                ],
                1 => [
                    'title'    => count($notes_obj).' Notes',
                    'subtitle' => '',
                    'rows'     => $notes_rows,
                ],
            ],  
        ];
        return json_encode($data);
    }

}
