<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MakeGroupsController extends Controller
{

    public $output = [];
    public $maxGroupSize = 15;

    public function make(Request $request)
    {
        if ($request->has('size'))
            $this->maxGroupSize = $request->size;

        $enabled = User::with('groups')->has('groups', '=', 1)
                        ->where('is_enabled', '=', 1)->where('is_hidden', '=', 0)->whereNull('deleted_at');

        $withJob = (clone $enabled)->whereNotNull('position')->get();
        $withoutJob = (clone $enabled)->whereNull('position')->get();

        if ($withJob->count() > $this->maxGroupSize)
            $this->breakdownEmployed($withJob);
        else 
            $this->output['Has a job'] = $withJob;

        if ($withoutJob->count() > $this->maxGroupSize)
            $this->breakdownUnemployed($withoutJob);

        $totalUserCount = 0;
        $includedUsers = collect();
        foreach($this->output as $users) {
            $totalUserCount += $users->count();
            $includedUsers = $includedUsers->merge($users);
        }

        $categories = Category::has('users', '>', 0)->get();

        $usersLeft = (clone $enabled)->whereNotIn('id', $includedUsers->pluck('id'))->get();
        $missingUsers = $usersLeft;
        $missingUserCount = $usersLeft->count();
        
        return view('admin.makegroups')->with([
            'maxSize' => $this->maxGroupSize,
            'groups' => $this->output,
            'totalUserCount' => $totalUserCount,
            'missingUsers' => $missingUsers,
            'missingUserCount' => $missingUserCount,
        ]);
    }

    protected function breakdownEmployed($withJob) 
    {
        $managesPeople = $withJob->where('manages_people', 1);
        $doesntManagePeople = $withJob->where('manages_people', 0);

        if ($managesPeople->count() > $this->maxGroupSize)
            $this->breakdownManagesPeople($managesPeople);
        else
            $this->output['Manages people'] = $managesPeople;
        
        if ($doesntManagePeople->count() > $this->maxGroupSize)
            $this->breakdownDoesntManagePeople($doesntManagePeople);
        else
            $this->output["Doesn't manage people"] = $doesntManagePeople;

    }

    public function breakdownManagesPeople($managesPeople)
    {
        $label = "Does Manage, Org Size: ";
        $test = [];
        foreach(array_merge(companySizes(), ["No org size selected"]) as $size) {
            $usersForOrgSize = User::whereIn('id', $managesPeople->pluck('id'))->where('company_size', '=', $size)->get();
            if ($usersForOrgSize->count() < $this->maxGroupSize && $usersForOrgSize->count() > 0)
                $this->output[$label . $size] = $usersForOrgSize;
            elseif ($usersForOrgSize->count() > 0)
                $this->breakdownByConsultantStatus($usersForOrgSize, $label);
        }
    }

    public function breakdownDoesntManagePeople($doesntManagePeople)
    {
        $label = "Doesn't Manage, Org Size: ";
        foreach(array_merge(companySizes(), ["no org size selected"]) as $size) {
            $orgSizeSelected = User::whereIn('id', $doesntManagePeople->pluck('id'))->where('company_size', '=', $size)->get();
            if ($orgSizeSelected->count() < $this->maxGroupSize && $orgSizeSelected->count() > 0)
                $this->output[$label . $size] = $orgSizeSelected;
            else if ($orgSizeSelected->count() > 0)
                $this->breakdownByConsultantStatus($orgSizeSelected, $label . ' ' . $size);
        }
    }

    public function breakdownUnemployed($withoutJob)
    {
        $unemployedSeeking = $withoutJob->where('is_unemployed_seeking', 1);
        $unemployedNotSeeking = $withoutJob->where('is_unemployed_seeking', 0);
        $students = $withoutJob->where('is_student', 1);
        $retired = $withoutJob->where('is_retired', 1);

        if($unemployedSeeking->count() > $this->maxGroupSize)
            $this->breakdownEducation($unemployedSeeking, "Unemployed Seeking Work ");
        else
            $this->output['Unemployed - Seeking Work'] = $unemployedSeeking;

        if($unemployedNotSeeking->count() > $this->maxGroupSize)
            $this->breakdownEducation($unemployedNotSeeking, "Unemployeed Not Seeking Work ");
        else
            $this->output['Unemployed - Not Seeking Work'] = $unemployedNotSeeking;

        if($students->count() > $this->maxGroupSize)
            $this->breakdownEducation($students, "Students ");
        else
            $this->output['Students'] = $students;
        
        if($retired->count() > $this->maxGroupSize)
            $this->breakdownEducation($retired, "Retired - ");
        else
            $this->output['Retired'] = $retired;
    }

    public function breakdownEducation($withoutJob, $label)
    {
        $usersWithSameEducation = [];
        $educations = education();
        $educations[] = 'Education not listed';
        foreach($educations as $education) {
            $usersForEducationLevel = $withoutJob->where('education', $education);
            if ($usersForEducationLevel->count() > 0)
                $this->output[$label . "- " . $education] = $usersForEducationLevel;
        }

    }

    public function breakdownByConsultantStatus($users, $label)
    {
        $isConsultantB2B = $users->filter(function ($user) {
            return $user->consultant_b2b == 1;
        });
        $isConsultantB2C = $users->filter(function ($user) {
            return ($user->consultant_b2c == 1 && $user->consultant_b2b == 0);
        });
        $notAConsultant = $users->filter(function ($user) {
            return ($user->consultant_b2b == 0 && $user->consultant_b2c == 0);
        });

        if ($isConsultantB2B->count() > 0)
            $this->output[$label . ' Consultant B2B'] = $isConsultantB2B;
        if ($isConsultantB2C->count() > 0)
            $this->output[$label . ' Consultant B2C'] = $isConsultantB2C;
        if ($notAConsultant->count() < $this->maxGroupSize)
            $this->breakdownByPosition($notAConsultant, $label . ', Not a consultant');
        elseif ($notAConsultant->count() > 0)
            $this->output[$label . ' Not a Consultant'] = $notAConsultant;
    }

    public function breakdownByPosition($users, $label)
    {
        $positionsUsersArray = [];
        foreach (positions() as $position) {
            $positionUsers = $users->where('position', $position);
            if ($positionUsers->count() > 0) {
                $this->output[$label . " - " . $position] = $positionUsers;
            }
        }
    }

}
