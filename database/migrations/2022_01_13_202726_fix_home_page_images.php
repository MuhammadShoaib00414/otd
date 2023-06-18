<?php

use App\HomePageImage;
use App\Option;
use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixHomePageImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        HomePageImage::each(function ($home_page_image) {
            $path = $home_page_image->getRawOriginal('image_url');
            if(substr($path, 0, 1) == '/')
            {
                $new_path = substr($path, 1);
                $home_page_image->update([
                    'image_url' => $new_path,
                ]);
            }
        });

        Setting::where('name', 'homepage_text')->update([
            'value' => 'This is the text that is viewable on the login page to give visitors an overview of what they can do on the platform.',
        ]);
        Setting::where('name', 'account_created_message')->update([
            'value' => 'This initial login screen is where you can first greet your community members/attendees. It’s a good place for a positive message, along with any basic guidelines for the community or event. All the text you see here can be customized.',
        ]);
        Setting::where('name', 'onboarding_popup')->update([
            'value' => 'This next window sets the stage for users to fill out their profiles and answer questions relevant to your community or event. It’s a good place to preface the type of information you’ll collect, and how it will be useful for their overall experience (i.e. help connect them to a curated network, help create their event itinerary based on preferences, etc.).',
        ]);
        Setting::where('name', 'from_email_name')->update([
            'value' => 'On The Dot Global',
        ]);
        Setting::where('name', 'name')->update([
            'value' => 'On The Dot Global',
        ]);
        Setting::where('name', 'is_management_chain_enabled')->update([
            'value' => 0,
        ]);
        Setting::where('name', 'is_departments_enabled')->update([
            'value' => 0,
        ]);





        Option::where('id', '>', 0)->delete();

        //  to anyone reading this, the next 200 or so lines were manually converted from an excel sheet. Hustles were easy,
        //  but when it needed to be an associative array, thats when things got ugly. I'm pretty sure this task has shortened
        //  my lifespan by roughly 6 days.

        $hustles = array("3D Printing", "Advising", "Advocacy", "Arts/Theater", "Bilingual/ Interpreter", "Business Development", "Career Counseling", "Communications", "Community Advocate", "Community Engagement", "Counseling/Emotional Health", "DEI (Diversity, Equity and Inclusion)", "Education", "Equine Therapy", "Executive/Leadership", "Facilitation", "Finance", "Formal Sciences", "Healthcare", "Hospitality", "Human Resources", "Insurance", "Investor/Managing Investments", "IT", "Leadership", "Leadership Coach, Transformational Coach", "Learning and Development", "Legal Services", "Life Science", "Management", "Marketing", "Mentoring", "Motivational/Business Speaker", "Nonprofits", "Parent", "People/Operations Management", "Philanthropy", "Physician", "Policy", "Professional Services", "Program/Product Management", "Public Relations", "Regulatory and Compliance", "Social Media", "Software Development", "Technology Commercialization", "Television/Production", "Therapy", "Training", "Volunteer Management", "Web Development", "Wellness", "Writing/Editing");

        foreach($hustles as $hustle)
        {
            Option::create([
                'taxonomy_id' => 1,
                'name' => $hustle,
                'is_enabled' => 1,
            ]);
        }


        $interests = array("3-D Printing" => 'Fun', 
            "Adoption" => 'Family & Parenting', 
            "Advocating for Women & Children" => 'Passions & Causes', 
            "Animal Welfare" => 'Passions & Causes', 
            "Animation" => 'Arts & Entertainment', 
            "Archery" => 'Active Wellness', 
            "Art Festivals" => 'Arts & Entertainment', 
            "Arts/Theater" => 'Arts & Entertainment', 
            "Baking" => 'Fun', 
            "Biking" => 'Active Wellness', 
            "Billiards" => 'Fun', 
            "Birds" => 'Animals/Pets', 
            "Blogging" => 'Fun',  
            "Camping" => 'Fun', 
            "Card Games" => 'Fun', 
            "Cars / Automotive" => 'Fun', 
            "Cats" => "Animals/Pets", 
            "Civil Rights" => "Passions & Causes", 
            "Coaching, Mentoring" => "Passions & Causes", 
            "Coding" => "Fun",
            "Coffee" => "Active Wellness", 
            "Community Organizer" => "Passions & Causes", 
            "Content Creation" => "Fun", 
            "Cooking" => "Fun", 
            "Creating Art" => "Fun", 
            "Creative Thinking" => "Passions & Causes", 
            "Dancing" => "Active Wellness", 
            "Digital Innovation" => "Fun", 
            "Diversity & Inclusion" => "Passions & Causes", 
            "Documentaries" => "Arts & Entertainment", 
            "Dogs" => "Animals/Pets", 
            "Education" => "Passions & Causes", 
            "Entrepreneurship" => "Passions & Causes", 
            "Environment" => "Passions & Causes", 
            "Equality" => "Passions & Causes", 
            "Exotic Pets" => "Animals/Pets", 
            "Faith" => "Passions & Causes", 
            "Family, Mentoring" => "Family & Parenting", 
            "Fashion" => "Fun", 
            "Financial Literacy/Equity" => "Passions & Causes", 
            "Fishing" => "Active Wellness", 
            "Foodie" => "Fun", 
            "Foster Parenting" => "Family & Parenting", 
            "Gaming" => "Fun", 
            "Gardening" => "Fun", 
            "Geneology" => "Fun", 
            "Golf" => "Active Wellness", 
            "Growing Businesses" => "Fun", 
            "Gym Workout" => "Active Wellness", 
            "Helping Veterans" => "Passions & Causes", 
            "Hiking" => "Active Wellness", 
            "History" => "Passions & Causes", 
            "Home Improvement" => "Fun", 
            "Horses" => "Animals/Pets", 
            "Indoor Cycling" => "Active Wellness", 
            "Investing" => "Fun", 
            "Justice Reform" => "Passions & Causes", 
            "Kayaking" => "Active Wellness", 
            "Knitting" => "Fun", 
            "Leadership & Personal Growth" => "Active Wellness", 
            "Live Music" => "Fun", 
            "Making Music" => "Fun", 
            "Making Videos" => "Fun", 
            "Marketing & Advertising" => "Fun", 
            "Martial Arts" => "Active Wellness", 
            "Meditation" => "Active Wellness", 
            "Mental Health" => "Passions & Causes", 
            "Veterans" => "Passions & Causes", 
            "Mindfulness" => "Active Wellness", 
            "Motivating Others" => "Passions & Causes", 
            "Motorcycles" => "Fun", 
            "Movies" => "Arts & Entertainment", 
            "Music" => "Arts & Entertainment", 
            "Networking" => "Fun", 
            "Paddle Boarding" => "Fun", 
            "Painting" => "Fun", 
            "Parenting" => "Family & Parenting", 
            "Personal Finance" => "Fun", 
            "Philanthropy" => "Passions & Causes", 
            "Photography" => "Fun", 
            "Podcasts" => "Arts & Entertainment", 
            "Politics" => "Passions & Causes", 
            "Reading - Fiction" => "Fun", 
            "Reading - Non Fiction" => "Fun", 
            "Rock Climbing" => "Active Wellness", 
            "Rom-Coms" => "Arts & Entertainment", 
            "Running" => "Active Wellness", 
            "Sailing" => "Fun", 
            "Scrapbooking" => "Fun", 
            "Scuba Diving" => "Active Wellness", 
            "Sewing / Crafts" => "Fun", 
            "Skateboarding" => "Fun", 
            "Sketching and Drawing" => "Fun", 
            "Skiing" => "Active Wellness", 
            "Snowboarding" => "Active Wellness", 
            "Social Justice Equity" => "Passions & Causes", 
            "Social Media" => "Fun", 
            "Spending Time Outdoors" => "Active Wellness", 
            "Spirituality" => "Active Wellness", 
            "Sports" => "Active Wellness", 
            "Stand-up comedy" => "Arts & Entertainment", 
            "Sudoku" => "Fun",
            "Swimming" => "Fun", 
            "TaiChi" => "Fun", 
            "TED Talks" => "Arts & Entertainment", 
            "Tennis" => "Active Wellness", 
            "Tiny Pets" => "Animals/Pets", 
            "TV" => "Arts & Entertainment", 
            "Volunteering / Non-Profits" => "Passions & Causes", 
            "Voting Rights" => "Passions & Causes", 
            "Walking" => "Active Wellness", 
            "Weight Lifting" => "Active Wellness", 
            "Wellness/Healthy Living" => "Passions & Causes", 
            "Woodworking" => "Fun", 
            "Word puzzles" => "Fun", 
            "Yoga" => "Active Wellness");

        foreach($interests as $name => $parent)
        {
            Option::create([
                'taxonomy_id' => 2,
                'name' => $name,
                'parent' => $parent,
                'is_enabled' => 1,
            ]);
        }

        $skillsets = array("Building Relationships" => "Business Skills", 
            "Networking" => "Business Skills", 
            "Presentation Skills" => "Business Skills", 
            "Product Management" => "Business Skills", 
            "Project Management" => "Business Skills", 
            "Public Speaking" => "Business Skills", 
            "Spreadsheet Formulas/Macros" => "Business Skills", 
            "Time Management" => "Business Skills", 
            "Contract Negotiation" => "Contracts / Regulatory", 
            "Government Contracts" => "Contracts / Regulatory", 
            "Intellectual Property" => "Contracts / Regulatory", 
            "Legal and Regulatory Issues" => "Contracts / Regulatory", 
            "Partnerships and Alliances" => "Contracts / Regulatory", 
            "Business Analysis" => "Data Analytics", 
            "Data Analytics" => "Data Analytics", 
            "Web Analytics" => "Data Analytics", 
            "Accounting Software" => "Finance", 
            "Budgeting" => "Finance", 
            "Cost Benefit Analysis" => "Finance", 
            "Financial Forecasting" => "Finance", 
            "Financial Management" => "Finance", 
            "Investor Relations" => "Finance", 
            "Profit & Loss Statements" => "Finance", 
            "Animation" => "Graphics & Design", 
            "Computer Graphics" => "Graphics & Design", 
            "Design" => "Graphics & Design", 
            "Graphic Design" => "Graphics & Design", 
            "Industrial Design" => "Graphics & Design", 
            "UX/UI Design" => "Graphics & Design", 
            "Hiring" => "Human Resources", 
            "Interviewing New Hires" => "Human Resources", 
            "Recruiting" => "Human Resources", 
            "Consumer Goods & Services" => "Industry Experience", 
            "Food & Beverage/Catering" => "ndustry Experience", 
            "Managing Cross Functional Teams" => "Management", 
            "Managing Underperforming Employees" => "Management", 
            "Managing Globally Dispersed Teams" => "Management", 
            "Motivating Employees" => "Management", 
            "People Management" => "Management", 
            "Corporate Communications" => "Marketing / Communications", 
            "Audio Production" => "Marketing / Communications", 
            "Advertising" => "Marketing / Communications", 
            "Affiliate Marketing" => "Marketing / Communications", 
            "Branding" => "Marketing / Communications", 
            "Competitive Analysis" => "Marketing / Communications", 
            "Content Creation" => "Marketing / Communications", 
            "Digital Marketing" => "Marketing / Communications", 
            "Digital Strategy" => "Marketing / Communications", 
            "E-commerce" => "Marketing / Communications", 
            "Event Management" => "Marketing / Communications", 
            "External Communications" => "Marketing / Communications", 
            "Global Marketing" => "Marketing / Communications", 
            "Marketing Campaigns" => "Marketing / Communications", 
            "Newsletters" => "Marketing / Communications", 
            "Planning Conferences" => "Marketing / Communications", 
            "Podcasting" => "Marketing / Communications", 
            "Press Releases" => "Marketing / Communications", 
            "Social Media Marketing/Advertising" => "Marketing / Communications", 
            "Translation" => "Marketing / Communications", 
            "Video Marketing" => "Marketing / Communications", 
            "Video Production" => "Marketing / Communications", 
            "Facilities Management" => "Operations", 
            "General Administration" => "Operations", 
            "Procurement" => "Operations", 
            "Purchasing" => "Operations", 
            "Supplier / Vendor Management" => "Operations", 
            "Supply Chain / Logistics" => "Operations", 
            "Customer/Client Relations" => "Sales", 
            "Sales Quotas" => "Sales", 
            "Sales Team Incentives" => "Sales", 
            "Cloud Computing" => "Software / Hardware", 
            "IT Architecture" => "Software / Hardware", "
            Artificial Intelligence" => "Software / Hardware", 
            "Automation" => "Software / Hardware", 
            "CRM Software" => "Software / Hardware", 
            "Game Development" => "Software / Hardware", 
            "Mobile Application Development" => "Software / Hardware", 
            "Quality Management" => "Software / Hardware", 
            "SEO" => "Software / Hardware", 
            "Software Testing" => "Software / Hardware", 
            "Web Design" => "Software / Hardware");

        foreach($skillsets as $name => $parent)
        {
            Option::create([
                'taxonomy_id' => 3,
                'name' => $name,
                'parent' => $parent,
                'is_enabled' => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
