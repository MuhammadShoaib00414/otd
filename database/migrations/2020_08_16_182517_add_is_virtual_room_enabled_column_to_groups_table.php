<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsVirtualRoomEnabledColumnToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->boolean('is_virtual_room_enabled')->default(0);
        });

        DB::insert("INSERT INTO `groups` (`id`, `name`, `created_at`, `updated_at`, `slug`, `deleted_at`, `description`, `header_bg_image_path`, `header_menu_json`, `is_budgets_enabled`, `is_files_enabled`, `is_discussions_enabled`, `is_shoutouts_enabled`, `parent_group_id`, `thumbnail_image_path`, `subgroups_page_name`, `files_alias`, `custom_menu`, `publish_to_global_feed`, `is_public`, `is_private`, `order_key`, `pinned_post_id`, `members_page_name`, `home_page_name`, `posts_page_name`, `content_page_name`, `calendar_page_name`, `shoutouts_page_name`, `discussions_page_name`, `is_virtual_room_enabled`) VALUES
('51', 'Default', '2020-08-25 16:18:49', '2020-08-25 16:18:49', 'all', NULL, NULL, NULL, NULL, '1', '1', '1', '1', NULL, NULL, 'Subgroups', NULL, NULL, '1', '1', '0', NULL, NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, '0');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn('is_virtual_room_enabled');
        });
    }
}
