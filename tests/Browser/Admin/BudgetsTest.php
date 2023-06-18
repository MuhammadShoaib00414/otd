<?php

namespace Tests\Browser\Admin;

use App\User;
use App\Budget;
use App\Expense;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BudgetsTest extends DuskTestCase
{
    /**
    * @group admins
    * @group budgets
    * @group probably-ok-if-this-is-broken
    *
    */
    public function testAdminCanCreateBudget()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();

            Expense::where('id', '>', 0)->delete();
            Budget::where('id', '>', 0)->delete();

            $browser->loginAs($user)
                    ->visit("/admin/budgets/create")
                    ->select('group_id')
                    ->type('total_budget', '1000')
                    ->press('Create budget')
                    ->assertSee('$1,000');
        });
    }

    /**
    * @group admins
    * @group budgets
    * @group probably-ok-if-this-is-broken
    *
    */
    public function testAdminCanAddBudgetExpense()
    {
        $this->browse(function (Browser $browser) {
            $user = User::where('is_admin', 1)->first();

            $budget = Budget::first();
            $description = Str::random(7);
            Expense::where('id', '>', 0)->delete();
            Budget::where('id', '>', 0)->delete();

            $browser->loginAs($user)
                    ->visit("/admin/budgets/create")
                    ->select('group_id')
                    ->type('total_budget', '1000')
                    ->press('Create budget')
                    ->click('@add_expense')
                    ->type('amount', '1')
                    ->select('category_id')
                    ->type('description', $description)
                    ->press('Save expense')
                    ->assertSee($description);
        });
    }
}
