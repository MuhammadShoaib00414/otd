<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('skill_user', function (Blueprint $table) {
            $table->integer('skill_id')->unsigned();
            $table->foreign('skill_id')
                  ->references('id')->on('skills')->onDelete('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')->on('users')->onDelete('cascade');

            $table->primary(['skill_id', 'user_id']);
        });

        \App\Skill::insert([
            ['name' => 'Accounting Software'],
            ['name' => 'Advertising'],
            ['name' => 'Affiliate Marketing'],
            ['name' => 'Animation'],
            ['name' => 'Artificial Intelligence'],
            ['name' => 'Audio Production'],
            ['name' => 'Automation'],
            ['name' => 'Branding'],
            ['name' => 'Budgeting'],
            ['name' => 'Building Relationships'],
            ['name' => 'Business Analysis'],
            ['name' => 'Cloud Computing'],
            ['name' => 'Competitive Analysis'],
            ['name' => 'Computer Graphics'],
            ['name' => 'Consumer Goods & Services'],
            ['name' => 'Content Creation'],
            ['name' => 'Contract Negotiation'],
            ['name' => 'Corporate Communications'],
            ['name' => 'Cost Benefit Analysis'],
            ['name' => 'CRM Software'],
            ['name' => 'Customer/Client Relations'],
            ['name' => 'Data Analytics'],
            ['name' => 'Design'],
            ['name' => 'Digital Marketing'],
            ['name' => 'Digital Strategy'],
            ['name' => 'E-commerce'],
            ['name' => 'Event Management'],
            ['name' => 'External Communications'],
            ['name' => 'Facilities Management'],
            ['name' => 'Financial Forecasting'],
            ['name' => 'Financial Management'],
            ['name' => 'Food & Beverage/Catering'],
            ['name' => 'Game Development'],
            ['name' => 'General Administration'],
            ['name' => 'Global Marketing'],
            ['name' => 'Government Contracts'],
            ['name' => 'Graphic Design'],
            ['name' => 'Hiring'],
            ['name' => 'Ideation'],
            ['name' => 'Industrial Design'],
            ['name' => 'Intellectual Property'],
            ['name' => 'Interviewing New Hires'],
            ['name' => 'Investor Relations'],
            ['name' => 'IT Architecture'],
            ['name' => 'Legal and Regulatory Issues'],
            ['name' => 'Managing Cross Functional Teams'],
            ['name' => 'Managing Underperforming Employees'],
            ['name' => 'Managing Globally Dispersed Teams'],
            ['name' => 'Marketing Campaigns'],
            ['name' => 'Mobile Application Development'],
            ['name' => 'Motivating Employees   '],
            ['name' => 'Networking'],
            ['name' => 'Newsletters'],
            ['name' => 'Partnerships and Alliances'],
            ['name' => 'People Management'],
            ['name' => 'Planning Conferences'],
            ['name' => 'Podcasting'],
            ['name' => 'Presentation Skills'],
            ['name' => 'Press Releases'],
            ['name' => 'Procurement'],
            ['name' => 'Product Management'],
            ['name' => 'Profit & Loss Statements'],
            ['name' => 'Project Management'],
            ['name' => 'Public Speaking'],
            ['name' => 'Purchasing'],
            ['name' => 'Quality Management'],
            ['name' => 'Recruiting'],
            ['name' => 'Sales Quotas'],
            ['name' => 'Sales Team Incentives'],
            ['name' => 'SEO'],
            ['name' => 'Social Media Marketing/Advertising'],
            ['name' => 'Software Testing'],
            ['name' => 'Spreadsheet Formulas/Macros'],
            ['name' => 'Supplier / Vendor Management'],
            ['name' => 'Supply Chain / Logistics'],
            ['name' => 'Time Management'],
            ['name' => 'Translations'],
            ['name' => 'UX/UI Design'],
            ['name' => 'Video Marketing'],
            ['name' => 'Video Production'],
            ['name' => 'Web Analytics'],
            ['name' => 'Web Design'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_user');
        Schema::dropIfExists('skills');
    }
}
