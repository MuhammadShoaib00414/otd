
	<tr>
					<td><a href="/admin/groups/{{ $group->id }}" target="_blank">{{ $group->name }}</a></td>
					<td>
						<input name="groups[{{ $group->id }}][is_private]" type="checkbox" class="mx-auto" {{ $group->is_private ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][is_private]" type="hidden" value="0" {{ $group->is_private ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][publish_to_parent_feed]" type="checkbox" class="mx-auto" {{ $group->publish_to_parent_feed ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][publish_to_parent_feed]" type="hidden" value="0" {{ $group->publish_to_parent_feed ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][is_virtual_room_enabled]" type="checkbox" class="mx-auto" {{ $group->is_virtual_room_enabled ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][is_virtual_room_enabled]" type="hidden" value="0" {{ $group->is_virtual_room_enabled ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][is_lounge_enabled]" type="checkbox" class="mx-auto" {{ ($group->lounge()->exists() && $group->lounge->is_enabled) ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][is_lounge_enabled]" type="hidden" value="0" {{ ($group->lounge()->exists() && $group->lounge->is_enabled) ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][enable_video_conference_in_lounge]" type="checkbox" class="mx-auto" {{ $group->enable_video_conference_in_lounge ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][enable_video_conference_in_lounge]" type="hidden" value="0" {{ $group->enable_video_conference_in_lounge ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][is_chat_room_enabled]" type="checkbox" class="mx-auto" {{ $group->is_chat_room_enabled ? 'checked' : '' }} value="1">
					</td>


					<td>
						<input name="groups[{{ $group->id }}][is_email_campaigns_enabled]" type="checkbox" class="mx-auto" {{ $group->is_email_campaigns_enabled ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][is_email_campaigns_enabled]" type="hidden" value="0" {{ $group->is_email_campaigns_enabled ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_ga_toggle_content_types]" type="checkbox" class="mx-auto" {{ $group->can_ga_toggle_content_types ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_ga_toggle_content_types]" type="hidden" value="0" {{ $group->can_ga_toggle_content_types ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][is_reporting_enabled]" type="checkbox" class="mx-auto" {{ $group->is_reporting_enabled ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][is_reporting_enabled]" type="hidden" value="0" {{ $group->is_reporting_enabled ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][is_reporting_user_data_enabled]" type="checkbox" class="mx-auto" {{ $group->is_reporting_user_data_enabled ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][is_reporting_user_data_enabled]" type="hidden" value="0" {{ $group->is_reporting_user_data_enabled ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_ga_set_live_chat]'" type="checkbox" class="mx-auto" {{ $group->can_ga_set_live_chat ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_ga_set_live_chat]'" type="hidden" value="0" {{ $group->can_ga_set_live_chat ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_group_admins_manage_virtual_room]" type="checkbox" class="mx-auto" {{ $group->can_group_admins_manage_virtual_room ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_group_admins_manage_virtual_room]" type="hidden" value="0" {{ $group->can_group_admins_manage_virtual_room ? 'disabled' : '' }}> 
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_group_admins_invite_other_groups_to_events]" type="checkbox" class="mx-auto" {{ $group->can_group_admins_invite_other_groups_to_events ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_group_admins_invite_other_groups_to_events]" type="hidden" value="0" {{ $group->can_group_admins_invite_other_groups_to_events ? 'disabled' : '' }}>
					</td>

					<td>
						<input name="groups[{{ $group->id }}][can_users_post_events]" type="checkbox" class="mx-auto" {{ $group->can_users_post_events ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_users_post_events]" type="hidden" value="0" {{ $group->can_users_post_events ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_users_post_shoutouts]" type="checkbox" class="mx-auto" {{ $group->can_users_post_shoutouts ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_users_post_shoutouts]" type="hidden" value="0" {{ $group->can_users_post_shoutouts ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_users_post_content]" type="checkbox" class="mx-auto" {{ $group->can_users_post_content ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_users_post_content]" type="hidden" value="0" {{ $group->can_users_post_content ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_users_post_text]" type="checkbox" class="mx-auto" {{ $group->can_users_post_text ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_users_post_text]" type="hidden" value="0" {{ $group->can_users_post_text ? 'disabled' : '' }}>
					</td>
					<td>
						<input name="groups[{{ $group->id }}][can_users_upload_files]" type="checkbox" class="mx-auto" {{ $group->can_users_upload_files ? 'checked' : '' }} value="1">
						<input name="groups[{{ $group->id }}][can_users_upload_files]" type="hidden" value="0" {{ $group->can_users_upload_files ? 'disabled' : '' }}>
					</td>

					<td><input name="groups[{{ $group->id }}][dashboard_header]" class="form-control" type="text" class="m-0" value="{{ $group->dashboard_header }}"></td>

					<td><input name="groups[{{ $group->id }}][home_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->home_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][posts_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->posts_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][content_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->content_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][calendar_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->calendar_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][shoutouts_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->shoutouts_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][discussions_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->discussions_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][subgroups_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->subgroups_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][members_page_name]" class="form-control pageName" type="text" class="m-0" value="{{ $group->members_page_name }}"></td>
					<td><input name="groups[{{ $group->id }}][files_alias]" class="form-control pageName" type="text" class="m-0" value="{{ $group->files_alias }}"></td>
				</tr>
@foreach($group->subgroups()->orderBy('order_key')->get() as $subgroup)
	@include('admin.groups.partials.bulkSettings.subgroupsRecursive', ['group' => $subgroup])
@endforeach