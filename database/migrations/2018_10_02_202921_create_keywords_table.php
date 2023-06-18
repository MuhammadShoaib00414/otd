<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keywords', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('keyword_user', function (Blueprint $table) {
            $table->integer('keyword_id')->unsigned();
            $table->foreign('keyword_id')
                  ->references('id')->on('keywords')->onDelete('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->primary(['keyword_id', 'user_id']);
        });

        \App\Keyword::insert([
            ['name' => 'empowerment'],
            ['name' => 'leadership'],
            ['name' => 'mentorship'],
            ['name' => 'women'],
            ['name' => 'women in business'],
            ['name' => 'women in tech'],
            ['name' => 'Feminist'],
            ['name' => 'Fundraising'],
            ['name' => 'SEO'],
            ['name' => 'podcast'],
            ['name' => 'digital content'],
            ['name' => 'visual content'],
            ['name' => 'global marketing'],
            ['name' => 'sales'],
            ['name' => 'career tips'],
            ['name' => 'diversity & inclusion'],
            ['name' => 'equality'],
            ['name' => 'Equal pay'],
            ['name' => 'investing'],
            ['name' => 'project management'],
            ['name' => 'building relationships'],
            ['name' => 'financial advice'],
            ['name' => 'Networking'],
            ['name' => 'collaboration'],
            ['name' => 'B2b'],
            ['name' => 'client relations'],
            ['name' => 'local business'],
            ['name' => 'conferences and conventions'],
            ['name' => 'affiliate marketing'],
            ['name' => 'advertising'],
            ['name' => 'press releases'],
            ['name' => 'editing'],
            ['name' => 'newsletters'],
            ['name' => 'video marketing'],
            ['name' => 'product branding'],
            ['name' => 'angel investing'],
            ['name' => 'recruiting'],
            ['name' => 'venture capital'],
            ['name' => 'hiring'],
            ['name' => 'Ideas'],
            ['name' => 'innovation'],
            ['name' => 'revenue'],
            ['name' => 'profitability'],
            ['name' => 'web analytics'],
            ['name' => 'finance'],
            ['name' => 'digital trends'],
            ['name' => 'software engineering'],
            ['name' => 'creative'],
            ['name' => 'freelancing'],
            ['name' => 'artists'],
            ['name' => 'culture'],
            ['name' => 'business plan'],
            ['name' => 'business management'],
            ['name' => 'Content creation'],
            ['name' => 'content strategy'],
            ['name' => 'business loan'],
            ['name' => 'consulting'],
            ['name' => 'business growth '],
            ['name' => 'blogging'],
            ['name' => 'brand awareness'],
            ['name' => 'opportunity'],
            ['name' => 'starting a business'],
            ['name' => 'retail'],
            ['name' => 'customers'],
            ['name' => 'Employees'],
            ['name' => 'value proposition'],
            ['name' => 'personal branding'],
            ['name' => 'social media'],
            ['name' => 'branding'],
            ['name' => 'Startup'],
            ['name' => 'creative business owner'],
            ['name' => 'maker'],
            ['name' => 'Business strategy'],
            ['name' => 'Female entrepreneur'],
            ['name' => 'success'],
            ['name' => 'role models'],
            ['name' => 'inspiration'],
            ['name' => 'email marketing'],
            ['name' => 'small business'],
            ['name' => 'IT'],
            ['name' => 'web design'],
            ['name' => 'women-led company'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keyword_user');
        Schema::dropIfExists('keywords');
    }
}
