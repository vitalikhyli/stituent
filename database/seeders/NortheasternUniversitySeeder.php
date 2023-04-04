<?php

namespace Database\Seeders;

use App\CommunityBenefit;
use App\Entity;
use App\Group;
use App\Partnership;
use App\PartnershipType;
use App\Person;
use App\Relationship;
use App\Team;
use App\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Seeder;

class NortheasternUniversitySeeder extends Seeder
{
    protected $team;
    protected $user;

    public function __construct()
    {
        parent::__construct(); // Errors out?
        $this->team = Team::where('old_cc_id', 409)->first();
        $this->user = User::where('username', 'disberg')->first();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $s = new DatabaseSeeder;
        $pro = 0;
        $this->command->info('Adding Northeastern Data...');

        $pilotpath = base_path().'/database/seeds/neu/pilots-2019.csv';
        $pilotfile = fopen($pilotpath, 'r');

        while ($row = fgetcsv($pilotfile)) {
            $pilot = new CommunityBenefit;
            $pilot->team_id = $this->team->id;
            $pilot->fiscal_year = 2019;

            $pilot->pilot = true;

            $pilot->program_name = $row[0];
            $pilot->program_description = $row[1];
            $pilot->value = str_replace([',', '$'], '', $row[2]);
            $pilot->value_type = $row[3];
            $pilot->time_frame = $row[4];

            $pilot->beneficiaries = $row[5];
            $pilot->initiators = $row[6];
            $pilot->partners = $row[7];

            $pilot->save();
        }

        // ORGANIZATIONS & PARTNERS

        $ptypes = ['Service Learning', 'Co-Curricular', 'MA Promise'];
        foreach ($ptypes as $ptype) {
            $partnership_type = new PartnershipType;
            $partnership_type->team_id = $this->team->id;
            $partnership_type->user_id = $this->user->id;
            $partnership_type->name = $ptype;
            $partnership_type->save();
        }

        $ccpath = base_path().'/database/seeds/neu/co-curricular-partners-2018-2019.csv';

        $year = '2018-01-01';

        $ccfile = fopen($ccpath, 'r');

        $count = 0;
        while ($row = fgetcsv($ccfile, 0, '|')) {
            $count++;
            if ($count == 1) {
                continue;
            }
            $entity = Entity::where('team_id', $this->team->id)
                            ->where('name', 'LIKE', trim($row[0]))
                            ->first();
            if (! $entity) {
                $entity = new Entity;
                $entity->team_id = $this->team->id;
                $entity->user_id = $this->user->id;
                $entity->name = trim($row[0]);
            }

            $entity->save();

            $programs = explode('/', trim($row[10]));
            foreach ($programs as $key => $program) {
                $pt_id = PartnershipType::where('team_id', $this->team->id)
                                ->where('name', 'Co-Curricular')
                                ->first()->id;

                $pship = new Partnership;
                $pship->team_id = $this->team->id;
                $pship->user_id = $this->user->id;
                $pship->partnership_type_id = $pt_id;

                $pship->partner_id = $entity->id;
                $pship->partner_name = $entity->name;
                $pship->program = $program;

                $contacts = [];
                if ($row[($key * 3) + 1]) {
                    $contact = [];
                    $contact['name'] = trim($row[($key * 3) + 1]).' '.trim($row[($key * 3) + 2]);
                    $contact['email'] = trim($row[($key * 3) + 3]);
                    $contact['phone'] = null;
                    $contacts[] = $contact;
                }
                $pship->contacts = $contacts;

                $pship->year = $year;

                $pship->save();
            }
        }

        //dd(Partnership::all());

        // ORGANIZATIONS & PARTNERS
        $mapromisepath = base_path().'/database/seeds/neu/ma_promise_community_partners_list_2019.csv';

        $year = '2018-01-01';

        $mapromisefile = fopen($mapromisepath, 'r');

        $count = 0;
        while ($row = fgetcsv($mapromisefile, 0, '|')) {
            $count++;
            if ($count == 1) {
                continue;
            }
            $entity = Entity::where('team_id', $this->team->id)
                            ->where('name', 'LIKE', trim($row[0]))
                            ->first();
            if (! $entity) {
                $entity = new Entity;
                $entity->team_id = $this->team->id;
                $entity->user_id = $this->user->id;
                $entity->name = trim($row[0]);
            }

            $entity->address_raw = $row[4]."\n".$row[5];
            $entity->address_city = $row[6];
            $entity->address_state = 'MA';
            $entity->address_zip = str_pad($row[7], 5, '0', STR_PAD_LEFT);

            $entity->save();

            $pt_id = PartnershipType::where('team_id', $this->team->id)
                                ->where('name', 'MA Promise')
                                ->first()->id;

            $pship = new Partnership;
            $pship->team_id = $this->team->id;
            $pship->user_id = $this->user->id;
            $pship->partnership_type_id = $pt_id;

            $pship->partner_id = $entity->id;
            $pship->partner_name = $entity->name;
            $pship->program = '';

            $contacts = [];
            $contact['name'] = trim($row[1]);
            $contact['email'] = trim($row[2]);
            $contact['phone'] = trim($row[3]);
            $contacts[] = $contact;
            $pship->contacts = $contacts;

            $pship->year = $year;

            $pship->save();
        }

        //dd(Partnership::all());
        // SERVICE LEARNING

        // "ARTD 2380 Video Basics"|"David Tames"|"CAMD"|4|"Michael Henetz"|"Boston Housing Authority"|"Sheyla Carew"|"Sheyla.Carew@bostonhousing.org"|4

        $servicelearningfile = base_path().'/database/seeds/neu/service-learning-community-partnerships-2019.csv';

        $year = '2019-01-01';

        $servicelearningfile = fopen($servicelearningfile, 'r');

        $courses_bad = ['DO NOT EMAIL',
                               'PARTNERSHIP WITHDRAWN',
                               'Service-Learning Course:',
                               null, ];

        $departments_bad = ['College', null];

        $partners_bad = ['Community Partner, Organization Name:', null];

        $faculty_bad = ['S-LTA', 'Faculty', null];

        $count = 0;

        while ($row = fgetcsv($servicelearningfile, 0, '|')) {
            $course = trim($row[0]);
            $faculty = trim($row[1]);
            $filer = trim($row[4]);
            $department = trim($row[2]);
            $partner = trim($row[5]);
            $partner_contact = trim($row[6]);
            $partner_email = trim($row[7]);
            $contact_info = [];
            $contact = [];
            $contact['name'] = $partner_contact;
            $contact['email'] = $partner_email;
            $contact['phone'] = null;

            if (! in_array($course, $courses_bad)) {
                $thecourse = $course;
            }

            if (! in_array($department, $departments_bad)) {
                $thedepartment = $this->seedFindOrCreate('entity', $this->departmentLookUp($department), null, 'NU Department'); //NOT NEU

                // // add groups
                // $catid = Category::where('name', 'partnerships')->first()->id;
                // $groupid = Group::where('category_id', $catid)
                //                 ->where('team_id', $this->team->id)
                //                 ->where('name', 'NEU Course')
                //                 ->first()->id;

                // $eg = new Partnership;
                // $eg->team_id = $this->team->id;
                // $eg->group_id = $groupid;
                // $eg->entity_id = $thedepartment->id;
                // $data = [];
                // $coursecontact = [];
                // $coursecontact['name'] = $faculty.", ".$filer;
                // $coursecontact['email'] = '';
                // $data['contacts'] = [];
                // $data['contacts'][] = $coursecontact;
                // $data['program'] = $thecourse;
                // $eg->data = $data;

                // $eg->save();
            }

            if (! in_array($partner, $partners_bad)) {
                $thepartner = $this->seedFindOrCreate('entity', $partner, $contact_info);
            }

            if (! in_array($faculty, $faculty_bad)) {
                $thefaculty = $faculty;
            }

            if (! in_array($filer, $faculty_bad)) {
                $thefiler = $filer;
            }
            $count++;

            $pt_id = PartnershipType::where('team_id', $this->team->id)
                                ->where('name', 'Service Learning')
                                ->first()->id;

            $service_learning = new Partnership;
            $service_learning->team_id = $this->team->id;
            $service_learning->user_id = $this->user->id;
            $service_learning->partnership_type_id = $pt_id;

            $data = [];
            $data['faculty'] = $thefaculty;
            $data['filer'] = $thefiler;
            $service_learning->data = $data;

            $service_learning->program = $thecourse;

            $service_learning->department_id = $thedepartment->id; //entities

            $service_learning->partner_id = $thepartner->id;
            $service_learning->partner_name = $partner;

            $contacts = [];
            $contact = [];
            $contact['name'] = $partner_contact;
            $contact['email'] = $partner_email;
            $contact['phone'] = null;
            $contacts[] = $contact;
            $service_learning->contacts = $contacts;

            $service_learning->year = $year;

            $service_learning->save();
        }

        $pro = $s->ProgressBar($pro, 1, 'Set of Data', 'static');
    }

    public function seedFindOrCreate($entity_or_person, $name, $contact_info, $entity_type = null)
    {
        if ($entity_or_person == 'entity') {
            if (Entity::where('name', $name)->where('team_id', $this->team->id)->doesntExist()) {
                $thing = new Entity;
                $thing->team_id = $this->team->id;
                $thing->user_id = $this->user->id;
                $thing->name = $name;
                $thing->type = $entity_type;
                $thing->save();

            //echo 'CREATED '.$entity_or_person.' ('.$entity_type.') '.$thing->name." \r\n";
            } else {
                $thing = Entity::where('name', $name)->where('team_id', $this->team->id)->first();
            }
        }
        if ($entity_or_person == 'person') {
            if (Person::where('full_name', $name)->where('team_id', $this->team->id)->doesntExist()) {
                $thing = new Person;
                $thing->team_id = $this->team->id;
                $thing->full_name = $name;
                $thing->first_name = trim(substr($name, 0, strrpos($name, ' ')));
                $thing->last_name = trim(substr($name, strrpos($name, ' ')));
                // $thing->contact_info = $contact_info;
                $thing->save();

            //echo 'CREATED '.$entity_or_person.' (person) '.$thing->full_name." \r\n";
            } else {
                $thing = Person::where('full_name', $name)->where('team_id', $this->team->id)->first();
            }
        }

        return $thing;
    }

    public function seedLink($subject, $subject_type, $kind, $object, $object_type)
    {
        if (Relationship::where('team_id', $this->team->id)
                        ->where('kind', $kind)
                        ->where('subject_id', $subject->id)
                        ->where('object_id', $object->id)
                        ->where('subject_type', $subject_type)
                        ->where('object_type', $object_type)
                        ->doesntExist()
            ) {
            $r = new Relationship;
            $r->team_id = $this->team->id; //NEU
            $r->kind = $kind;
            $r->subject_id = $subject->id;
            $r->object_id = $object->id;
            $r->subject_type = $subject_type;
            $r->object_type = $object_type;
            $r->save();
        }
    }

    public function departmentLookUp($acronym)
    {
        $college_aliases = ['CAMD'     => 'College of Arts, Media and Design (CAMD)',
                            'COS'      => 'College of Science (COS)',
                            'CSSH'     => 'College of Social Sciences and Humanities (CSSH)',
                            'CCIS'     => 'Khoury College of Computer Sciences (CCIS)',
                            'CPS'      => 'College of Professional Studies (CPS)',
                            'Explore'  => 'Explore Program for Undeclared Students',
                            'COE'      => 'College of Engineering (COE)',
                            'Bouve'    => 'Bouv&eacute; College of Health Sciences',
                           ];

        if (! array_key_exists($acronym, $college_aliases)) {
            return $acronym;
        } else {
            return $college_aliases[$acronym];
        }
    }
}
