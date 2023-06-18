<?php

use App\RegistrationPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_pages', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('slug');
            $table->json('assign_to_groups')->nullable();
            $table->boolean('is_event_only')->default(0);
            $table->boolean('is_welcome_page_accessible')->default(0);
            $table->json('localization')->nullable();
            $table->timestamps();
        });

        // RegistrationPage::create([
        //     'name' => 'Default',
        //     'description' => '',
        //     'slug' => 'default',
        //     'is_welcome_page_accessible' => true,
        // ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_pages');
    }
}
