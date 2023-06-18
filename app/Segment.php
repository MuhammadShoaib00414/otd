<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
      protected $guarded = ['id'];

      protected $casts = [
          'filters' => 'object',
          'start_date' => 'date',
          'end_date' => 'date',
      ];

      public function applyFilters($users)
      {
          $users->whereNull('deleted_at');
          if ($this->filters) {
              $filters = $this->filters;
              if (isset($filters->groups) && count($filters->groups)) {
                  $users->leftJoin('group_user', 'group_user.user_id', '=', 'users.id')
                          ->whereIn('group_id', $filters->groups);
              }
              if (false && isset($filters->filters)) {
                  foreach($filters->filters as $filter) {
                    if (isset($filter->parameter) && $filter->parameter != '') {
                      if ($filter->object == 'location') {
                          if ($filter->expression == 'contains')
                              $users->where('location', 'LIKE', '%'.$filter->parameter.'%');
                          elseif ($filter->expression == 'does-not-contain')
                              $users->where('location', 'NOT LIKE', '%'.$filter->parameter.'%');
                      } elseif ($filter->object == 'skills') {
                          if ($filter->expression == 'contains') {
                              $users->leftJoin('skill_user', 'users.id', '=', 'skill_user.user_id')
                                    ->leftJoin('skills', 'skill_user.skill_id', '=', 'skills.id')
                                    ->where('skills.name', 'LIKE', '%'.$filter->parameter.'%');
                          } elseif ($filter->expression == 'does-not-contain') 
                          {
                              $users->leftJoin('skill_user', 'users.id', '=', 'skill_user.user_id')
                                    ->leftJoin('skills', 'skill_user.skill_id', '=', 'skills.id')
                                    ->where('skills.name', 'NOT LIKE', '%'.$filter->parameter.'%');
                          }
                      } elseif ($filter->object == 'hustles') {
                          if ($filter->expression == 'contains') {
                              $users->leftJoin('category_user', 'users.id', '=', 'category_user.user_id')
                                    ->leftJoin('categories', 'category_user.category_id', '=', 'categories.id')
                                    ->where('categories.name', 'LIKE', '%'.$filter->parameter.'%');
                          } elseif ($filter->expression == 'does-not-contain') 
                          {
                              $users->leftJoin('category_user', 'users.id', '=', 'category_user.user_id')
                                    ->leftJoin('categories', 'category_user.category_id', '=', 'categories.id')
                                    ->where('categories.name', 'NOT LIKE', '%'.$filter->parameter.'%');
                          }
                      } elseif ($filter->object == 'personal-interests') {
                          if ($filter->expression == 'contains') {
                              $users->leftJoin('keyword_user', 'users.id', '=', 'keyword_user.user_id')
                                    ->leftJoin('keywords', 'keyword_user.keyword_id', '=', 'keywords.id')
                                    ->where('keywords.name', 'LIKE', '%'.$filter->parameter.'%');
                          } elseif ($filter->expression == 'does-not-contain') 
                          {
                              $users->leftJoin('keyword_user', 'users.id', '=', 'keyword_user.user_id')
                                    ->leftJoin('keywords', 'keyword_user.keyword_id', '=', 'keywords.id')
                                    ->where('keywords.name', 'NOT LIKE', '%'.$filter->parameter.'%');
                          }
                      } elseif ($filter->object == 'badges') {
                          if ($filter->expression == 'contains') {
                              $users->leftJoin('badge_user', 'users.id', '=', 'badge_user.user_id')
                                    ->leftJoin('badges', 'badge_user.badge_id', '=', 'badges.id')
                                    ->where('badges.name', 'LIKE', '%'.$filter->parameter.'%');
                          } elseif ($filter->expression == 'does-not-contain') 
                          {
                              $users->leftJoin('keyword_user', 'users.id', '=', 'keyword_user.user_id')
                                    ->leftJoin('keywords', 'keyword_user.keyword_id', '=', 'keywords.id')
                                    ->where('keywords.name', 'NOT LIKE', '%'.$filter->parameter.'%');
                          }
                      } elseif ($filter->object == 'job-title') {
                          if ($filter->expression == 'contains')
                              $users->where('job_title', 'LIKE', '%'.$filter->parameter.'%');
                          elseif ($filter->expression == 'does-not-contain')
                              $users->where('job_title', 'NOT LIKE', '%'.$filter->parameter.'%');
                      }
                    }
                  }
              }
          }
          return $users;
      }

      public function getUserIdsAttribute()
      {
          return $this->applyFilters(\DB::table('users'))->where('users.is_enabled', 1)->where('users.is_hidden', 0)->select('users.id')->pluck('users.id')->unique();
      }

      public function getUsersAttribute()
      {
          return User::whereIn('id', $this->user_ids)->distinct()->orderBy('users.name', 'asc')->get()->unique();
      }

      public function userQuery()
      {
        return User::whereIn('id', $this->user_ids)->distinct()->orderBy('users.name', 'asc');
      }

      public function getFolderSafeName()
      {
        $folder_safe_name = str_replace('/', '', $this->name);
        $unique_safe_name = $folder_safe_name . $this->id;
        return $unique_safe_name;
      }
}
