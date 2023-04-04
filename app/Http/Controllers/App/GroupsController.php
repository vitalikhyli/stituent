<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Group;

class GroupsController extends Controller
{
    use LinksTrait;

    public function index()
    {
        if (request('next') == 'archived') {
            return $this->archived();
        }
        $categories = Auth::user()->categories->sortBy('name');

        $rows = [];
        foreach ($categories as $category) {

            $groups = $category->groups()
                               ->whereNull('archived_at')
                               ->get()
                               ->sortBy('name');

            $links = $this->getGroupsLinks($groups);


            $rows[] = ['title' => $category->name,
                       'text'  => '',
                       'links' => $links];
        }

        $data = [
            'header'   => 'Groups',
            'next_url' => 'app/groups?next=archived',
            'sections' => [
                0 => [
                    'title'    => '',
                    'subtitle' => 'Highlighted groups were updated recently.',
                    'rows'     => [],
                ],
                1 => [
                    'title'    => 'Active Groups',
                    'subtitle' => '',
                    'rows'     => $rows,
                ],
            ],
        ];
        return json_encode($data);
    }

    public function archived()
    {

        $categories = Auth::user()->categories->sortBy('name');

        $rows = [];
        foreach ($categories as $category) {

            $groups = $category->groups()
                               ->whereNotNull('archived_at')
                               ->get()
                               ->sortBy('name');

            $links = $this->getGroupsLinks($groups);

            if (count($links) < 1) {
                continue;
            }

            $rows[] = ['title' => $category->name,
                       'text'  => '',
                       'links' => $links];
        }

        $data = [
            'header'   => 'Groups',
            'next_url' => '',
            'sections' => [
                0 => [
                    'title'    => 'Archived Groups',
                    'subtitle' => 'These groups are still available in the system but are not considered active.',
                    'rows'     => $rows,
                ],
            ],
        ];
        return json_encode($data);
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            $id = Group::inRandomOrder()->first()->id;
        }

    	$group = Group::find($id);

        $people = $this->getPersonLinks($group->people);
    	$files = $this->getFilesLinks($group->files);

    	$data = [
            'header' => $group->name,
            'next_url' => '',
            'sections' => [
                0 => [
                    'title'    => $group->name,
                    'subtitle' => ''.$group->category_name,
                    'rows'     => [
                        0 => [
                            'title'  => 'Description',
                            'text'   => ''.$group->notes,
                            'links'  => '',
                        ],
                        1 => [
                            'title'  => 'Files',
                            'text'   => '',
                            'links'  => $files,
                        ],
                        2 => [
                            'title'  => 'People',
                            'text'   => '',
                            'links'  => $people,
                        ],
                        
                    ]
                ],
            ],
        ];
        return json_encode($data);
    }
}
