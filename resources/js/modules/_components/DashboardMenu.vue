<template>
	<div>
		<div>
			<router-link :to="'/home'" class="nav-link bg-primary-100 hover:text-primary-800" href="/home"
				style="border-radius: .25rem;"><i class="icon-home mr-1"></i> {{
					$__.messages['my-dashboard']
				}}</router-link>
			<a class="nav-link bg-primary-100 hover:text-primary-800 d-md-none" href="/search"><i
					class="icon-magnifying-glass mr-1"></i> {{ $__.messages['search'] }}</a>
			<a class="nav-link hover:text-primary-800" :href="'/users/' + $user.id"><i class="icon-user mr-1"></i> {{
				$__.messages['my-profile']
			}}</a>
			<a class="nav-link hover:text-primary-800 d-lg-none" href="/account"><i class="icon-briefcase mr-1"></i> {{
				$__.messages['account']
			}}</a>
			<a class="nav-link hover:text-primary-800 d-lg-none" href="/notifications"><svg width="1em" height="1em"
					viewBox="0 0 16 16" class="bi bi-bell-fill mr-1" fill="currentColor"
					xmlns="http://www.w3.org/2000/svg">
					<path
						d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z" />
				</svg> {{ $__.messages['notifications'] }}
				<red-dot v-if="$user.hasUnreadNotifications"></red-dot>
			</a>
			<a class="nav-link hover:text-primary-800" href="/messages"><i class="icon-mail mr-1"></i> {{
				$__.messages['messages']
			}}
				<red-dot v-if="dashboardNotifications && dashboardNotifications.unread_message_count"></red-dot>
			</a>
			<a v-if="$settings.is_ideations_enabled" class="nav-link hover:text-primary-800" href="/ideations"><i
					class="icon-light-bulb mr-1"></i> {{ $__.messages['ideations'] }}
				<red-dot v-if="dashboardNotifications && dashboardNotifications.has_ideation_notifications"></red-dot>
			</a>
            
			<a v-if="this.base_url != 'https://todayisagoodday.onthedotglobal.com' " class="nav-link hover:text-primary-800" href="/introductions"><i class="icon-network mr-1"></i> {{
				$__.messages['introductions']
			}}
				<red-dot
					v-if="dashboardNotifications && dashboardNotifications.has_introduction_notifications"></red-dot>
			</a>
			<a v-if="this.base_url != 'https://todayisagoodday.onthedotglobal.com' " class="nav-link hover:text-primary-800" href="/shoutouts/received"><i class="icon-megaphone mr-1"></i> {{
				$__.messages['shoutouts']
			}}
				<red-dot v-if="dashboardNotifications && dashboardNotifications.has_shoutout_notifications"></red-dot>
			</a>
		
			<a class="nav-link hover:text-primary-800" href="/calendar"><i class="icon-calendar mr-1"></i> {{
				$__.messages['home.leftnav.calendar']
			}}
				<red-dot v-if="dashboardNotifications && dashboardNotifications.has_event_notifications"></red-dot>
			</a>
			<a class="nav-link hover:text-primary-800" href="/browse"><i class="icon-magnifying-glass mr-1"></i> {{
				$settings.find_your_people_alias
			}}</a>
			<a v-if="$settings.is_ask_a_mentor_enabled" class="nav-link hover:text-primary-800" href="/mentors/ask"><i
					class="icon-chat mr-1"></i> {{ $settings.ask_a_mentor_alias }}</a>
			<router-link to="/groups/join" class="nav-link hover:text-primary-800"><i class="icon-users mr-1"></i> {{
				$__.messages['Join a Group']
			}}</router-link>
			<a v-if="$user.is_admin" class="nav-link" href="/admin"><i class="icon-briefcase mr-1"></i> {{
				$__.messages['home.leftnav.admin']
			}}</a>
			<a class="nav-link d-md-none" href="/logout"
				onclick="return confirm('Are you sure you want to logout?');"><i class="icon-log-out mr-1"></i> {{
					$__.messages['logout']
				}}</a>
		</div>
		<div>


			<div v-if="!settings">
				<page-skeleton></page-skeleton>
			</div>
			<div v-if="is_admin_settings == '1'">
				<div class="mt-3">
					<div class="mb-3">
						<h5 class="text-uppercase text-muted mb-1 mt-1" style="font-size: 14px;">{{
							settings
						}}</h5>
						<div class="nav flex-column" v-for="page in pages" :key="page.id">
							<a v-if="page.is_active == '1' && page.displayed_show == 'on'"
								class="d-block mb-1 cursor-pointer"
								@click="showpop(page.id, '/pages/' + page.id + '/' + page.slug, this)">
								{{ page.title }}</span></a>
						</div>
						<span>
							<a v-if="pages.length >= 6" id="slug" class="p-1 hover:text-primary-800 badge"
								:href="'/pages/' + settings.replace(/\s+/g, '-').toLowerCase()">
								Load more..</a>
						</span>
					</div>
				</div>
			</div>
		</div>
		<div>

			<div v-if="!$user.dashboard_groups_list">
				<group-menu-skeleton></group-menu-skeleton>
			</div>
			<div v-for="(groups, groupHeader) in $user.dashboard_groups_list">
				<h5 class="text-uppercase text-muted mb-1 mt-1" style="font-size: 14px;">{{ groupHeader }}</h5>
				<div v-for="group in groups">
					<router-link v-if="group" :to="'/groups/' + group.slug" class="d-block mb-1">{{
						group.name
					}}</router-link>
					<div v-if="group.subgroups.length" v-for="subgroup in group.subgroups" class="ml-2">
						<router-link v-if="subgroup" :to="'/groups/' + subgroup.slug" class="d-block mb-1">{{
							subgroup.name
						}}</router-link>
						<div v-if="subgroup.subgroups.length" v-for="subsubgroup in subgroup.subgroups" class="ml-2">
							<router-link v-if="subsubgroup" :to="'/groups/' + subsubgroup.slug" class="d-block mb-1">{{
								subsubgroup.name
							}}</router-link>
							<div v-if="subsubgroup.subgroups.length" v-for="subsubsubgroup in subsubgroup.subgroups"
								class="ml-2">
								<router-link v-if="subsubsubgroup" :to="'/groups/' + subsubsubgroup.slug"
									class="d-block mb-1">{{ subsubsubgroup.name }}</router-link>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>


</template>
<style>

</style>
<script>
import RedDot from './RedDot';
import GroupMenuSkeleton from './../skeleton/GroupsListSkeleton';
import PageSkeleton from './../skeleton/PageSkeleton';

export default {

	data() {
		return {
			pages: [],
			settings: '',
			is_admin_settings: '',
			element: '',
			windowwidth: '',
			showpopModel: false,
			base_url:'',
		}
	},

	components: {
		RedDot,
		GroupMenuSkeleton,
		PageSkeleton,
	},
	created() {
		axios.get('/api/pages')
			.then((response) => {
				
				if (response.data.hasOwnProperty('pages')) {
					this.pages = response.data.pages;
					this.settings = response.data.settings.value;
					this.is_admin_settings = response.data.is_admin_settings.value;
					this.is_url = response.data.is_url.value;
				} else {
					this.pages = false;
					this.settings = true;
					this.is_admin_settings = false;
				}
			})
			.catch((error) => {
				this.settings = false;
			});
			axios.get('/api/settings')
			.then((response) => {
				this.base_url = response.data.base_url;
				
			})
			.catch((error) => {
				this.settings = false;
			});
	},


	methods: {
		showpop(id, obj_url, obj) {
			var iframe = '<iframe src="' + obj_url + '"  style="background: #fff;height: 75vh!important;width: 75vw !important;"></iframe>';
			$('#modal_content').modal('show');
			$('#pop-up').html(iframe);
		},
	}
}
</script>