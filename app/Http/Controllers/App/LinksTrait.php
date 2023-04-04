<?php

namespace App\Http\Controllers\App;
use Carbon\Carbon;
use Auth;

trait LinksTrait {

	//========================================> NOTES
	
	function getNotesLinks($notes) {
    $links = [];

        foreach ($notes as $note) {
          $icons = $note->activity_icons;
            $links[] = ['id'     => ''.$note->id,
                      'name' => ''.$note->name,
                      'type' => 'note',
                      'high' => ($note->requires_followup ? '1' : '0'),
                      'date' => ''.$note->created_at->format('n/j/Y'),
                      'icon' => $icons,
                      'text' => ''.substr($note->notes, 0, 100),
                    ];
        }
    return $links;
  }

  function getNotesRows($notes, $field) {
    $grouped_date = $notes->groupBy($field);
    $rows = [];    
      foreach ($grouped_date as $groupdate => $notes) {
          $links = $this->getNotesFullTextLinks($notes);
          $rows[] = ['title' => $groupdate,
               'text'  => '',
               'links' => $links];
      }
      return $rows;
  }

  function getNotesFullTextLinks($notes) {
    $links = [];

        foreach ($notes as $note) {
          $icons = $note->activity_icons;
            $links[] = ['id'     => ''.$note->id,
                      'name' => ''.$note->name,
                      'type' => 'note',
                      'high' => ($note->requires_followup ? '1' : '0'),
                      'date' => ''.$note->created_at->format('n/j/Y'),
                      'icon' => $icons,
                      'text' => ''.$note->notes,
                    ];
        }
    return $links;
  }

	//========================================> CASES
	function getCasesLinks($cases) {
		$links = [];
        $curr_user_id = Auth::user()->id;
        foreach ($cases as $case) {

            //$text = $case->id." ";
            $text = $case->app_text;
            $icons = $case->activity_icons;
            $links[] = ['id'     => ''.$case->id,
                      'name' => ''.$case->name,
                      'type' => 'case',
                      'high' => ($case->user_id == $curr_user_id ? '1' : '0'),
                      'date' => ''.$case->created_at->format('n/j/Y'),
                      'icon' => $icons,
                      'text' => $text,
                    ];
        }
        return $links;
    }

    // ========================================> GROUP-PERSON
    function getGroupPersonLinks($gps) {
    	$links = [];
        foreach ($gps as $gp) {

            if (!$gp->group) {
                continue;
            }
            $people = $gp->group->getPeopleAddedOnDate($gp->ud);

            if ($gp->group->category) {
	            if ($gp->group->category->has_position) {
	                // $positions = $group->people->groupBy('position');
	                // foreach ($positions as $position => $people) {
	                //     if ($position == '') {
	                //         $position = 'No Position';
	                //     }
	                //     $text .= $position.": ".$group->people_count."\n";
	                // }
	                $people .= "\n".$gp->group->people_count." total people";
	            } else {
	                $people .= "\n".$gp->group->people_count." total people";
	            }
	        }

	        $icons = $gp->group->activity_icons;
            $links[] = ['id'     => ''.$gp->group->id,
                      'name' => ''.$gp->group->name,
                      'type' => 'group',
                      'high' => '1',
                      'date' => '',
                      'icon' => $icons,
                      'text' => ''.$people,
                    ];
        }
        return $links;
    }

    // ========================================> PERSON
    function getPersonLinks($people) {
    	$links = [];
        foreach ($people as $person) {

        	$icons = $person->activity_icons;
            $links[] = ['id'     => ''.$person->id,
                      'name' => ''.$person->name.($person->dob ? ', '.$person->getRawOriginal('age') : ''),
                      'type' => 'constituent',
                      'high' => '1',
                      'date' => '',
                      'icon' => $icons,
                      'text' => ''.$person->town_address,
                    ];
        }
        return $links;
    }

    function getGroupsLinks($groups)
    {
    	$links = [];
        foreach ($groups as $group) {

            $text = "";
            if ($group->category) {
	            if ($group->category->has_position) {
	                // $positions = $group->people->groupBy('position');
	                // foreach ($positions as $position => $people) {
	                //     if ($position == '') {
	                //         $position = 'No Position';
	                //     }
	                //     $text .= $position.": ".$group->people_count."\n";
	                // }
	                $text .= $group->people_count." people";
	            } else {
	                $text .= $group->people_count." people";
	            }
	        }
	        $archived_text = "";
	        if ($group->archived_at) {
	        	$archived_text = 'ARCHIVED - '.$group->archived_at->format('n/j/Y')."\n";
	        }
	        $icons = $group->activity_icons;
            $links[] = ['id'     => ''.$group->id,
                      'name' => ''.$group->name,
                      'type' => 'group',
                      'high' => ($group->updated_recently ? '1' : '0'),
                      'date' => ''.$group->created_at->format('n/j/Y'),
                      'icon' => $icons,
                      'text' => $archived_text.$text,
                    ];
        }
        return $links;
    }

    // ========================================> VOTERS 
    function getVoterLinks($voters) {
    	$links = [];
		foreach ($voters as $voter) {

			$links[] = ['id' 	 => ''.$voter->id,
						  'name' => $voter->name.($voter->dob ? ', '.$voter->getRawOriginal('age') : ''),
					      'type' => 'constituent',
                          'high' => '0',
					  	  'date' => '',
					  	  'icon' => [],
					  	  'text' => $voter->town_address,
					  	];
		}
		return $links;
	}

    function getVoterBirthdayLinks($voters) {
    	$links = [];
		foreach ($voters as $voter) {


    		$nextbday = $voter->next_birthday;

    		$daysaway = '+'.Carbon::today()->diffInDays($nextbday).' days';
    		if ($nextbday == Carbon::today()) {
    			$daysaway = 'Today';
    		}
    		if ($nextbday == Carbon::tomorrow()) {
    			$daysaway = 'Tomorrow';
    		}
			
			$icons = $voter->activity_icons;

			$links[] = ['id' 	 => ''.$voter->id,
						  'name' => $voter->name.($voter->dob ? ', '.$voter->getRawOriginal('age') : ''),
					      'type' => 'constituent',
                          'high' => (is_numeric($voter->id) ? '1' : '0'),
					  	  'date' => '',
					  	  'icon' => $icons,
					  	  'text' => $voter->town_address."\n".$daysaway." (".$voter->dob->format('n/j/Y').")",
					  	];
		}
		return $links;
	}

	// ========================================> ORGANIZATIONS
    function getOrganizationsLinks($orgs) {
	
		$links = [];
		foreach ($orgs as $org) {
			$links[] = [
				'id'	=> ''.$org->id,
				'name'	=> ''.$org->name,
				'type'	=> 'org',
				'date'	=> '',
				'icon'	=> [],
				'text'	=> ''.trim($org->description."\n".$org->address."\n".$org->social_media),
			];
		}
		return $links;
	}

	// ========================================> FILES
    function getFilesLinks($files) {
    	$links = [];
		foreach ($files as $file) {
            $links[] = ['id'     => ''.$file->id,
                         'name' => ''.$file->name,
                         'type' => 'file',
                         'high' => '0',
                         'date' => ''.$file->created_at->format('n/j/Y'),
                         'icon' => '',
                         'text' => '',
                        ];
        }
        return $links;
    }
}