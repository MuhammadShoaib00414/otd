<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLocalizationColumnToTaxonomiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('article_posts', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('discussion_threads', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('files', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('folders', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('ideation_articles', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('ideation_surveys', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('ideations', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('lounges', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('options', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('points', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });

        Schema::table('titles', function (Blueprint $table) {
            $table->json('localization')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('article_posts', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('discussion_threads', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('folders', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

       	Schema::table('ideation_articles', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('ideation_surveys', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('ideations', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('lounges', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('points', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('localization');
        });

        Schema::table('titles', function (Blueprint $table) {
            $table->dropColumn('localization');
        });
    }
}
