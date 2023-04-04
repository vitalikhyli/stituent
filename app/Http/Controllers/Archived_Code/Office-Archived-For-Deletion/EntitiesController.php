<?php
/* NOT SURE THIS CONTROLLER IS NEEDED -- NO ENTITIES IN OFFICE APP

namespace App\Http\Controllers\Office;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Entity;

use Auth;

use Validator;

class EntitiesController extends Controller
{

    private static $blade = 'office';
    private static $dir = '/office';


    public function editContact($entity, $contact_id)
    {
        $entity = Entity::find($entity);
        $thecontact = Contact::find($contact_id);

        $this->authorize('basic', $entity);
        $this->authorize('basic', $thecontact);

        $form_action = '/entities/'.$entity->id.'/contacts/'.$thecontact->id;

        return view(self::$blade.'.contacts.edit', compact('thecontact','form_action'));
    }


    public function save(Request $request)
    {
        $entity = new Entity;
        $entity->name       = request('name');
        $entity->team_id    = Auth::user()->team->id;
        $entity->save();

        return redirect(self::$blade.'/entities/'.$entity->id.'/edit');
    }


    public function new($v)
    {
        $name = trim($v);

        return view(self::$blade.'.entities.new',compact('name'));
    }


    public function search($search_value)
    {
        $search_value = trim($search_value);

        if (!$search_value) { return null; }

        $entities = Entity::where('team_id',Auth::user()->team->id)
                          ->where('name','like','%'.$search_value.'%')
                          ->get();

        return view(self::$blade.'.entities.list',compact('entities', 'search_value'));
    }

    public function edit($id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        return view(self::$blade.'.entities.edit',compact('entity'));
    }

    public function update(Request $request, $id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        //VALIDATION START
        $validate_array = $request->all();

        // PROCESS EMAILS + ADD TO VALIDATION
        // $email_number = request('email_number');
        // $email_main = request('email_main');
        // $emails_array = array();
        // for ($e=1; $e<=$email_number; $e++) {
        //     if (request('email_'.$e) != null) {
        //         ($e == $email_main) ? $main = 1 : $main = 0;
        //         $validate_array['emails'][] = request('email_'.$e);
        //         $emails_array[] = array(
        //             "main" => $main,
        //             "email" => request('email_'.$e),
        //             "notes" => request('email_notes_'.$e),
        //             );
        //     }
        // }

        // PROCESS PHONES + ADD TO VALIDATION
        // $phone_number = request('phone_number');
        // $phone_main = request('phone_main');
        // $phones_array = array();
        // for ($e=1; $e<=$phone_number; $e++) {
        //     if (request('phone_'.$e) != null) {
        //         ($e == $phone_main) ? $main = 1 : $main = 0;
        //         $phones_array[] = array(
        //             "main" => $main,
        //             "phone" => request('phone_'.$e),
        //             "notes" => request('phone_notes_'.$e),
        //             );
        //     }
        // }

        $contacts = [];
        foreach ($request->all() as $key => $value) {
            if(substr($key,0,5) == 'name_') {
                $contact_id        = substr($key,5);
                if (
                    (request('name_'.$contact_id) != null) ||
                    (request('phone_'.$contact_id) != null) ||
                    (request('email_'.$contact_id) != null)
                ) {

                    $contacts[] = ['name'   =>  request('name_'.$contact_id),
                                   'phone'   =>  request('phone_'.$contact_id),
                                   'email'  =>  request('email_'.$contact_id)];
                }
            }
        }

        // VALIDATE
        $validator = Validator::make($validate_array, [
                'name' => ['required', 'max:255'],
                'emails.*' => ['email'],
        ]);
        if ($validator->fails()) {
            return back()
                    ->withErrors($validator)
                    ->withInput();
        }

        // UPDATE RECORD
        $entity->private            = request('private');
        $entity->name               = request('name');
        $entity->address_street     = request('address_street');
        $entity->address_number     = request('address_number');
        $entity->address_fraction   = request('address_fraction');
        $entity->address_apt        = request('address_apt');
        $entity->address_city       = request('address_city');
        $entity->address_state      = substr(request('address_state'),0,2);
        $entity->address_zip        = substr(request('address_zip'),0,5);
        // $entity->email              = json_encode($emails_array);
        // $entity->phone              = json_encode($phones_array);
        $entity->contact_info       = $contacts;

        $entity->save();

        // COMPOUND FIELDS
        $entity->full_address       = $entity->generateFullAddress();
        $entity->household_id       = $entity->generateHouseholdId();

        $entity->save();

        session()->flash('msg', 'Entity was Saved!');

        if (request('save_and_close')) {
            return redirect(self::$dir.'/entities/'.$entity->id);
        } else {
            return redirect(self::$dir.'/entities/'.$entity->id.'/edit');
        }
    }

    public function show($id)
    {
        $entity = Entity::find($id);

        $this->authorize('basic', $entity);

        $tab = Auth::user()->getmemory('entity_tabs', 'relationships');

        return view(self::$blade.'.entities.show', compact('entity',
                                                           'tab'));

    }

    public function index()
    {
        $entities = Entity::where('team_id',Auth::user()->team->id)
                          ->orderBy('name')
                          ->get();

        return view(self::$blade.'.entities.index', compact('entities'));
    }

}
