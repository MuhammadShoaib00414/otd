<?php

namespace App\View\Components;

use App\Taxonomy;
use Illuminate\View\Component;

class UsersQuery extends Component
{
    public $rules;
    public $query;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($query = "{}")
    {
        $this->rules = $this->getRules();
        $this->query = $query;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.users-query');
    }

    protected function getRules()
    {
        $rules = collect([]);

        $taxonomies = Taxonomy::enabled();

        foreach ($taxonomies as $taxonomy) {
            $choices = collect([]);

            foreach ($taxonomy->options_with_users as $option) {
                $choices[] = (Object) [
                    'label' => $option->name,
                    'value' => $option->id,
                ];
            }

            $rules[] = (Object) [
                'type' => 'select',
                'id' => 'taxonmy'.$taxonomy->id,
                'label' => $taxonomy->singular_name,
                'choices' => $choices,
            ];
        }

        return $rules;
    }
}
