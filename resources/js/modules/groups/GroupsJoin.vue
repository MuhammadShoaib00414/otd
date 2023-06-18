<template>
	<div>
		<div class="container-fluid pt-3">
            <div class="row">
                <div class="col-3 d-none d-md-block">
                	<router-link to="/">< {{ $__.messages['Dashboard'] }}</router-link>
                    <dashboard-menu :groups="$usersGroups"></dashboard-menu>
                </div>
                <div class="col-12 col-md-9">
                	<div class="d-flex justify-content-start flex-wrap">
	                    <div class="spinner" v-show="!haveGroupsLoaded"></div>
	                    <div v-for="group in groups" class="col-md-4">
	                    	<div class="mb-3">
					            <router-link :to="'/groups/'+group.slug" class="card h-100" style="text-decoration: none;">
					              <div :style="'background-image: url('+group.thumbnail+');'" style="background-color: #eee; background-size: cover; background-position: center;">
                                    <span v-if="group.group_member"  class="ribbon top-right ribbon-success">
                                    <small>Joined</small>
                                    </span>
                                    <div style="width: 100%; margin-top: 51%;"></div>
					              </div>
					              <div class="card-body text-center font-weight-bold d-flex flex-column justify-content-center align-items-center">
					                <span style="text-decoration: none;">{{ group.name }}</span>
					                <div v-if="group.description">
					                	<p style="text-decoration: none;" class="text-muted font-weight-normal">{{ group.description }}</p>
					                </div>
					              </div>
					            </router-link>
					        </div>
					    </div>
                    </div>
                    <div v-if="groups.length == 0">
				    	<div class="card w-100 py-5 text-center" style="background-color: rgba(128, 128, 128, 0.1);">
				    		<b>No joinable groups found.</b>
				    	</div>
				    </div>
                </div>
            </div>
        </div>
	</div>
</template>

<script>
	import DashboardMenu from '../_components/DashboardMenu'

	export default {
		data() {
			return {
				haveGroupsLoaded: false,
				groups: [],
			}
		},
		components: {
			DashboardMenu,
		},
		created() {
			axios.get('/api/groups/joinable')
				.then((response) => {
					this.groups = response.data;
					this.haveGroupsLoaded = true;
				});
		},
	}

</script>
