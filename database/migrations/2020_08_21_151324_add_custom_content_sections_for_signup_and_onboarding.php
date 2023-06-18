<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomContentSectionsForSignupAndOnboarding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('settings')->insert([
            [
                'name' => 'homepage_text',
                'value' => 'Welcome to the ultimate resource to help you build your inner-circle and expand your community of resources. Find the right and perfect people to help you get things done and get where you want to go. Be part of our amazing network of resources that help others along the way. Together we are unstoppable.',
            ],
            [
                'name' => 'account_created_message',
                'value' => 'On The Dot is a network of professional women at all different career levels who are seeking connection, camaraderie and community. Mentoring within the platform is based on skillset, and anyone regardless of their career level can provide mentorship to others based on your unique skills. It is not a job searching or matching platform per se, but of course growing an expanded professional network via relationship building many times leads to the best career moves. It is definitely about finding and making connections in your field, and also making connections with other women who are in similar positions across industries and share the same challenges. It is not a place to find clients or customers, as soliciting members on the platform is strictly forbidden. The platform is here to foster true kinship and support among professional women. If you agree to these terms, we’re excited to have you join us.',
            ],
            [
                'name' => 'onboarding_popup',
                'value' => 'Get Ready to Make Some Great Connections!

Your profile helps us to create an optimal networking experience by matching you to your professional peers. We know that when we make connections based on similar roles, experiences, shared passions and interests, those connections are valuable and have great staying power.

It only takes a few minutes to tell us who you are - let’s get started!',
            ],
            [
                'name' => 'open_registration',
                'value' => '0'
            ],
            [
                'name' => 'hide_new_members',
                'value' => '0'
            ],
            [
                'name' => 'group_admins',
                'value' => 'nothing'
            ],
            [
                'name' => 'registration_key',
                'value' => \Illuminate\Support\Str::random(8),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('settings')->where('name', '=', 'homepage_text')->delete();
        \DB::table('settings')->where('name', '=', 'account_created_message')->delete();
        \DB::table('settings')->where('name', '=', 'onboarding_popup')->delete();
        \DB::table('settings')->where('name', '=', 'open_registration')->delete();
        \DB::table('settings')->where('name', '=', 'hide_new_members')->delete();
        \DB::table('settings')->where('name', '=', 'group_admins')->delete();
        \DB::table('settings')->where('name', '=', 'registration_key')->delete();
    }
}
