<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VoterMaster;
use App\Person;
use App\Voter;
use Auth;

class ConstituentsController extends Controller
{
    use LinksTrait;
    public function show($id)
    {

        $voter = null;
        if (!$id) {
            $id = 'MA_04LDD2083003';
        }

        if (is_numeric($id)) {
            $person = Person::thisTeam()->where('id', $id)->first();
            if ($person) {
                $voter = $person->voter;
                if (!$voter) {
                    $voter = $person;
                }
            }
        } else {
            $voter = Voter::find($id);
            if (!$voter) {
                $voter = Voter::find('MA_04LDD2083003');
            }
            $person = Person::thisTeam()->where('voter_id', $id)->first();
        }
        if (!$voter) {
            return json_encode(['Error' => 'Voter not found on this account']);
        }

        $data = [];

        // ==========================> HEADERS
        $name = $voter->name;
        if ($person && $person->name) {
            $name = $person->name;
        }
        $data['name'] = $name;

        $data['activity'] = [];
        if ($person) {
            $data['activity']['shared_cases']   = $person->sharedCases()->count();
            $data['activity']['cases']          = $person->cases()->count();
            $data['activity']['groups']         = $person->groups()->count();
            $data['activity']['notes']          = $person->contacts()->count();
        }


        // ==========================> GENERAL INFO

        $title = 'General Info';

        $section = [];
        $section['subtitle'] = '';

        $fields = [];

        if ($person) {
            $nickname = "";
            if ($person->nickname) {
                $first = "";
                if ($voter->first_name != $person->first_name) {
                    $first = $voter->first_name;
                }
                if (!$first) {
                    $first = $person->getAttributes()['first_name'];
                }
                $fields['Voter Name'] = "$first\nPreferred: ".$person->nickname;
            }        
        }
        

        $fields['Voter Address'] = $voter->full_address;
        if ($person) {
            if ($person->full_address != $voter->full_address) {
                $fields['Address'] = $person->full_address;
            }
        }

        $cohabitators = $this->getVoterLinks($voter->cohabitators());
        
        if ($person) {
            $person_cohab = $this->getPersonLinks($person->cohabitators());
            $cohabitators = array_merge($cohabitators, $person_cohab);
        }



        // ==========================> CONTACT INFO


        if (request('debug')) {
            dd($data);
        }

        $person_rows = [];

        if ($person) {
            // ==========================> CASES

            $cases_links = $this->getCasesLinks($person->cases);


            // ==========================> FILES
            $files_links = $this->getCasesLinks($person->files);

            // ==========================> GROUPS
            $groups_links = $this->getGroupsLinks($person->groups);

            // ==========================> NOTES
            $notes_links = $this->getNotesLinks($person->contacts);

            // ==========================> BULK EMAILS


            $person_section = [
                'title'    => Auth::user()->team->name.' Data',
                'subtitle' => '',
                'rows'     => [
                    0 => [
                        'title' => 'Cases',   
                        'text'  => '',
                        'links' => $cases_links,
                    ],
                    1 => [
                        'title' => 'Groups',   
                        'text'  => '',
                        'links' => $groups_links,
                    ],
                    2 => [
                        'title' => 'Files',   
                        'text'  => '',
                        'links' => $files_links,
                    ],
                    3 => [
                        'title' => 'Notes',   
                        'text'  => '',
                        'links' => $notes_links,
                    ]
                ],
            ];

        }



        // ==========================> VOTER DATA

        $data = [
            'header' => $voter->name,
            'create_note' => [
                'page'  => 'constituent',
                'id'    => ''.$voter->id,
                'show'  => '1',
            ],
            'sections' => [
                0 => [
                    'title'    => 'General Info',
                    'subtitle' => '',
                    'rows'     => [
                        0 => [
                            'title' => 'Voter File Name',   
                            'text'  => $voter->name,
                            'links' => [],
                        ],
                        1 => [
                            'title' => 'Address',        
                            'text'  => $voter->full_address,
                            'links' => [],
                        ],
                        2 => [
                            'title' => 'Residents',         
                            'text'  => '',
                            'links' => $cohabitators,
                        ],
                        3 => [
                            'title' => 'Born/Gender',       
                            'text'  => "".$voter->age,
                            'links' => [],
                        ],
                    ]
                ],
                1 => [
                    'title'    => 'Contact Info',
                    'subtitle' => 'Here is a sub title',
                    'rows'     => [
                        0 => [
                            'title' => 'Master Email List',   
                            'text'  => ''.$voter->master_email_list,
                            'links' => [],
                        ],
                        // 1 => [
                        //     'title' => 'Emails',   
                        //     'text'  => ''.$voter->emails,
                        //     'links' => [],
                        // ],
                        // 2 => [
                        //     'title' => 'Phones',   
                        //     'text'  => ''.$voter->phones,
                        //     'links' => [],
                        // ],
                        // 3 => [
                        //     'title' => 'Social Media',   
                        //     'text'  => ''.$voter->social_media,
                        //     'links' => [],
                        // ],
                        // 4 => [
                        //     'title' => 'Business',   
                        //     'text'  => ''.$voter->business,
                        //     'links' => [],
                        // ],
                    ]
                ],
            ],
        ];
        if ($person) {
            $data['sections'][] = $person_section;
        }
        return json_encode($data);
    }
}
