<?php

use App\Category;
use App\Keyword;
use App\Skill;
use App\Taxonomy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CopyOverExistingCategoriesAndUsersToTaxonomies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->copyOverCategories();
        $this->copyOverKeywords();
        $this->copyOverSkills();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }

    protected function copyOverCategories()
    {
        $newCat = Taxonomy::create([
            'name' => getSetting('categories_title'),
            'description' => getSetting('categories_description'),
            'is_enabled' => (getSetting('is_categories_enabled') == true) ? 1 : 0,
        ]);
        $categories = Category::all();
        foreach ($categories as $category) {
            $option = $newCat->options()->create([
                'name' => $category->name,
                'parent' => $category->parent,
                'created_by' => $category->created_by,
                'is_enabled' => $category->is_enabled,
            ]);
            $option->users()->attach($category->users);
        }
    }

    protected function copyOverKeywords()
    {
        $newCat = Taxonomy::create([
            'name' => getSetting('keywords_title'),
            'description' => getSetting('keywords_description'),
            'is_enabled' => (getSetting('is_keywords_enabled') == true) ? 1 : 0,
        ]);
        $keywords = Keyword::all();
        foreach ($keywords as $keyword) {
            $option = $newCat->options()->create([
                'name' => $keyword->name,
                'parent' => $keyword->parent,
                'created_by' => $keyword->created_by,
                'is_enabled' => $keyword->is_enabled,
            ]);
            $option->users()->attach($keyword->users);
        }
    }

    protected function copyOverSkills()
    {
        $newCat = Taxonomy::create([
            'name' => getSetting('skills_title'),
            'description' => getSetting('skills_description'),
            'is_enabled' => (getSetting('is_skills_enabled') == true) ? 1 : 0,
        ]);
        $skills = Skill::all();
        foreach ($skills as $skill) {
            $option = $newCat->options()->create([
                'name' => $skill->name,
                'parent' => $skill->parent,
                'created_by' => $skill->created_by,
                'is_enabled' => $skill->is_enabled,
            ]);
            $option->users()->attach($skill->users);
        }
    }
}
