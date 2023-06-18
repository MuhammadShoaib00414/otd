<?php

namespace App;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taxonomy extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'localization' => 'array',
    ];

    public function options()
    {
        return $this->hasMany(Option::class)->where('options.is_enabled', 1);
    }

    public function getSingularNameAttribute()
    {
        return singular($this->name);
    }

    public static function public()
    {
        return self::where('is_public', '=', 1)
            ->where('is_enabled', '=', 1)
            ->orderBy('name', 'asc')
            ->get();
    }

    public static function enabled()
    {
        return self::where('is_enabled', '=', 1)
            ->orderBy('name', 'asc')
            ->get();
    }

    public static function editable()
    {
        return self::where('is_enabled', '=', 1)
            ->where('is_user_editable', '=', 1)
            ->whereHas('options')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getGroupedOptionsAttribute()
    {
        return $this->options()->where('is_enabled', 1)->get()->mapToGroups(function ($option, $key) {
            return [$option[''] => $option];
        });
    }

    public function getOptionsWithUsersAttribute()
    {
        return $this->options()->orderBy('name', 'asc')->where('is_enabled', 1)->whereHas('users', function ($query) {
            return $query->where('users.id', '!=', request()->user()->id);
        }, '>', '0')->get();
    }

    public function getGroupedOptionsWithUsersAttribute()
    {
        return $this->options_with_users->mapToGroups(function ($option, $key) {
            return [$option['parent'] => $option];
        });
    }

    public function customgroupedOptionsWithOrderKey($key, $hasUsers = true, $selection = [])
    {
        if ($hasUsers)
            $options = $this->options_with_users;
        else {
            if (!empty($selection))
                $options = $this->options()->where('is_enabled', 1)->get($selection);
            else
                $options = $this->options()->where('is_enabled', 1)->get();
        }

        $options = $options->sortBy(function ($option) use ($key) {
            return (count(explode('-', $option[$key . '_order_key'])) == 2) ? explode('-', $option[$key . '_order_key'])[1] : null;
        });

        return $options->mapToGroups(function ($option, $key) {
            return [$option['parent'] => $option['id']];
        });
    }

    public function groupedOptionsWithOrderKey($key, $hasUsers = true)
    {
        if ($hasUsers)
            $options = $this->options_with_users;
        else
            $options = $this->options()->where('is_enabled', 1)->get();

        // $options = $options->sortBy(function ($option) use ($key) {
        //     return (count(explode('-', $option[$key.'_order_key'])) == 2) ? explode('-', $option[$key.'_order_key'])[1] : null;
        // });
        $options = $options->sortBy(function ($option) use ($key) {
            $orderKey = $option[$key . '_order_key'];
            $parts = explode('-', $orderKey);

            // Check if the order key has two parts and return the second part,
            // otherwise return a default value (e.g., 0) as the sort key
            return count($parts) == 2 ? $parts[1] : 0;
        });


        return $options->mapToGroups(function ($option, $key) {
            return [$option['parent'] => $option];
        })->sortBy(function ($options) use ($key) {
            return $options->first()->parentOrderKey($key);
        });
    }

    public function groupedOptionsWithOrderKeyCategories($key, $hasUsers = true)
    {
        if ($hasUsers) {
            $options = $this->options_with_users;
        } else {
            $options = $this->options()->where('is_enabled', 1)->get();
        }

        $options = $options->sortBy(function ($option) use ($key) {
            $orderKey = $option[$key . '_order_key'];
            $parts = explode('-', $orderKey);

            // Check if the order key has two parts and return the second part,
            // otherwise return a default value (e.g., 0) as the sort key
            return count($parts) == 2 ? $parts[1] : 0;
        });

        return $options->mapToGroups(function ($option, $key) {
            return [$option['parent'] => $option];
        })->sortBy(function ($options) use ($key) {
            return $options->first()->parentOrderKey($key);
        })->sortKeys();
    }




    public function groupedOptionsWithOrderKeyParent($key, $hasUsers = true)
    {
        if ($hasUsers) {
            $options = $this->options_with_users;
        } else {
            $options = $this->options()->where('is_enabled', 1)->get();
        }

        $options = $options->sortBy(function ($option) use ($key) {
            $orderKey = $option[$key . '_order_key'];
            // Convert the order key to a number and return it for sorting
            return intval($orderKey);
        });

        return $options->mapToGroups(function ($option, $key) {
            return [$option['parent'] => $option];
        })->sortBy(function ($options) use ($key) {
            return $options->first()->parentOrderKey($key);
        });
    }






    public function getOptionsWithMentorsAttribute()
    {
        return $this->options()->orderBy('name', 'asc')->where('is_enabled', 1)->whereHas('users', function ($query) {
            return $query->where('is_mentor', 1)
                ->where('users.id', '!=', request()->user()->id);
        }, '>', '0')->get();
    }

    public function getOrderedGroupedOptionsWithMentorsAttribute()
    {
        return $this->options_with_mentors->sortBy(function ($option) {
            return $option->orderKey('mentor');
        })->mapToGroups(function ($option, $key) {
            return [$option[''] => $option];
        })->sortBy(function ($options) {
            return $options->first()->parentOrderKey('mentor');
        });
    }

    public function getOrderedGroupedOptionsAttribute()
    {
        return $this->options()->where('is_enabled', 1)->get()->sortBy(function ($option) {
            return $option->orderKey('browse');
        })->mapToGroups(function ($option, $key) {
            return [$option[''] => $option];
        })->sortBy(function ($options) {
            return $options->first()->parentOrderKey('browse');
        });
    }

    public function getOrderedGroupedOptionsWithUsersAttribute()
    {
        return $this->options_with_users->sortBy(function ($option) {
            return $option->orderKey('browse');
        })->mapToGroups(function ($option, $key) {
            return [$option[''] => $option];
        })->sortBy(function ($options) {
            return $options->first()->parentOrderKey('browse');
        });
    }

    public function orderedGroupedOptionsWithUsersBelongingToGroup($group)
    {
        if ($group == null)
            return $this->getOrderedGroupedOptionsWithUsersAttribute();

        return $this->options()
            ->orderBy('name', 'asc')
            ->where('is_enabled', 1)
            ->whereHas('users', function ($query) use ($group) {
                return $query->where('users.id', '!=', request()->user()->id)
                    ->whereIn('users.id', $group->users->pluck('id'))
                    ->where('is_hidden', 0)
                    ->where('is_enabled', 1);
            })->get()->sortBy(function ($option) {
                return $option->orderKey('browse');
            })->mapToGroups(function ($option, $key) {
                return [$option[''] => $option];
            })->sortBy(function ($options) {
                return $options->first()->parentOrderKey('browse');
            });
    }

    public function getNameAttribute($value)
    {
        return localizedValue('name', $this->localization) ?: $value;
    }

    public function getDescriptionAttribute($value)
    {
        return localizedValue('description', $this->localization) ?: $value;
    }

    public function attributeWithLocale($attribute, $locale)
    {
        if ($locale == 'en')
            return $this[$attribute];

        if (isset($this->localization[$locale])) {
            if (isset($this->localization[$locale][$attribute])) {
                return $this->localization[$locale][$attribute];
            }
        }
        return null;
    }
}
