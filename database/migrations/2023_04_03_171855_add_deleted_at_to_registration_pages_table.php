<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToRegistrationPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('registration_pages', 'deleted_at')) {
            Schema::table('registration_pages', function (Blueprint $table) {
                $table->softDeletes()->after('purchased_warning_button_text');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('registration_pages', 'deleted_at')) {
            Schema::table('registration_pages', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }
}
