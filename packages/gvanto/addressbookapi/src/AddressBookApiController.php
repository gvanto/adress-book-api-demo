<?php

namespace gvanto\addressbookapi;

use gvanto\addressbookapi\Models\Email;
use gvanto\addressbookapi\Models\Group;
use gvanto\addressbookapi\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class AddressBookApiController
{
    /**
     * @var AddressBookService
     */
    protected $service;

    public function __construct(AddressBookService $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addPerson(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
                'first_name' => 'required|min:2|max:100',
                'last_name' => 'required|min:2|max:100',
                'emails' =>  'required|array',
                'emails.*' => 'email|unique:emails,email',
                'phone_numbers' => 'required|array',
                'phone_numbers.*' => ['regex:/^[0-9]+$/i'], //for simplicity just ensure all digits
                'addresses' =>  'required|array',
                'addresses.*' => 'string|min:12|max:100',
                'groups' =>  'required|array',
                'groups.*' => 'string|min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        $person = $this->service->createPerson(
            $request->post('first_name'),
            $request->post('last_name'),
            $request->post('phone_numbers'),
            $request->post('addresses'),
            $emails = $request->post('emails')
        );

        // Create any new groups (or use existing) and add person to it
        foreach ($request->post('groups') as $groupName) {
            /** @var Group $group */
            $group = Group::where('name', $groupName)->first();
            if (!$group) {
                $group = $this->service->createGroup($groupName);
            }
            $group->persons()->attach($person);
        }

        return response()->json([
            'message' => 'Person successfully created.',
            'person' => $person->toArray(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getPersonGroups(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        /** @var Person $person */
        $person = Person::find($request->get('id'));

        if (!$person) {
            return response()->json('Person not found.', 404);
        }

        return response()->json([
            'resultCount' => $person->groups->count(),
            'groups' => $person->groups->toArray(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addGroup(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:100|unique:groups,name',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        $group = $this->service->createGroup($request->post('name'));

        return response()->json([
            'message' => sprintf('Group %s (id=%d) successfully created.',
                $group->name,
                $group->id
            ),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getGroupMembers(Request $request): JsonResponse
    {
        // Allow 'group' to either be the name or id
        $validator = Validator::make($request->all(), [
            'group' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        /** @var Group $group */
        $group = (!is_numeric($request->get('group'))) ?
            Group::where('name', $request->get('group'))->first() :
            Group::find((int)$request->get('group')); //find by id

        if (!$group) {
            return response()->json('Group not found.', 404);
        }

        return response()->json([
            'resultCount' => $group->persons->count(),
            'members' => $group->persons->toArray(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findPersonByName(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'min:2|max:100',
            'last_name' => 'min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        $person = null;
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');

        if ($first_name && $last_name) {
            $person = Person::where('first_name', $first_name)
                ->where('last_name', $last_name)
                ->first();
        } else if ($first_name && !$last_name) {
            $person = Person::where('first_name', $first_name)->first();
        } else if (!$first_name && $last_name) {
            $person = Person::where('last_name', $last_name)->first();
        } else {
            return response()->json(
                'Either first_name, last_name (or both) must be supplied.',
                422
            );
        }

        return response()->json(($person) ? $person->toArray() : ['Person not found.']);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findPersonByEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|min:2|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        $email = $request->get('email');

        // cater for more than one result
        /** @var Collection $persons */
        $persons = null;
        $query = Person::leftJoin('emails', 'persons.id', 'emails.person_id');

        if (strpos($email, '@') !== false) {
            $persons = $query->where('emails.email', $email)->get();
        } else {
            $persons = $query->where('emails.email', 'like', $email . '%')->get();
        }

        return response()->json([
            'resultCount' => $persons->count(),
            'person(s)' => $persons->toArray(),
        ]);
    }
}
