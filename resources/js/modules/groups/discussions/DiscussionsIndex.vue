<template>
	<div>
		<div class="mb-2 pl-2 pt-2">
            <router-link to="/home">{{ $__.messages['Dashboard'] }}</router-link><span class="px-1">></span>
            <router-link :to="'/groups/'+$route.params.slug">{{ group.name }}</router-link>
        </div>
        <div class="row">
            <div class="col-3 d-none d-md-block">
                <group-menu :group="group" :isUserAdminOfGroup="isUserAdminOfGroup"></group-menu>
            </div>
            <div class="col-12 col-md-8">
            	<div class="d-block d-md-none px-2">
                    <button class="btn btn-primary w-100 mb-2" @click="showGroupMenu = !showGroupMenu">{{ $__.groups['Group Menu'] }}</button>
                    <group-menu :group="group" :isUserAdminOfGroup="isUserAdminOfGroup" v-show="showGroupMenu" @click="showGroupMenu = false"></group-menu>
                </div> 
            	<div class="d-flex mb-2 justify-content-between align-items-center px-2">
            		<h4 class="mb-0">{{ group.discussions_page_name }} ({{ discussions.length }})</h4>
            		<form method="get" :action="'/groups/'+group.slug+'/discussions'">
			            <div class="input-group">
			                <input type="text" class="form-control" name="q" placeholder="Search for...">
			                <div class="input-group-prepend">
			                    <button type="submit" class="btn btn-light" style="border: 1px solid #ced4da; border-left: 0; background-color: #fff; color: #1a2b40;">
			                        <i class="icon-magnifying-glass"></i>
			                    </button>
			                </div>
			            </div>
			        </form>
			        <a :href="'/groups/'+group.slug+'/discussions/create'" class="btn btn-secondary btn-sm">{{ $__.discussions['New Discussion'] }}</a>
            	</div>
            	<div class="card mx-2">
                	<table class="table mb-0">
			            <router-link tag="tr" v-for="thread in discussions" :key="thread.id" :to="'/groups/'+group.slug+'/discussions/'+thread.slug" style="cursor:pointer">
			                <td style="width: 3em;">
			                    <div :style="'background-image: url(`'+thread.owner.photo_path+'`)'" style="height: 2.75em; width: 2.75em; border-radius: 50%; background-size: cover; background-position: center;">
			                    </div>
			                </td>
			                <td>
			                    <b>{{ thread.name }}</b><br>
			                    <span>{{ thread.owner.name }}</span>
			                </td>
			                <td style="vertical-align: middle;">
			                    <i class="icon-chat mr-1"></i> {{ thread.post_count }}
			                </td>
			                <td class="text-right" style="vertical-align: middle;">
			                    {{ thread.updated_at | humanReadableDate }}
			                </td>
			            </router-link>
			        </table>
			    </div>
            </div>
        </div>
	</div>
</template>

<script>
	import GroupMenu from '../_components/GroupMenu'
	import moment from 'moment'

export default {
	props: [],
	data() {
		return {
			isUserAdminOfGroup: false,
			group: {
                name: '',
                slug: '',
                id: null,
                custom_menu: '',
                discussions_page_name: 'Discussions',
            },
            discussions: {},
            showGroupMenu: false,
		}
	},
	created() {
			axios.get('/api/groups/'+this.$route.params.slug+'/discussions')
				.then((response) => {
                    this.discussions = response.data;
                 })

			axios.get('/api/groups/'+this.$route.params.slug)
				.then((response) => {
                    this.group = response.data;
                    this.$usersGroups.forEach((group) => {
                        if (group.id == this.group.id && group.pivot.is_admin) {
                            this.isUserAdminOfGroup = true;
                        }
                    })
                 })
	},
	filters: {
        humanReadableDate: function (value) {
          if (!value) return ''
          return moment(value).local().format('M/D/YY')
        },
        humanReadableTime: function (value) {
          if (!value) return ''
          return moment(value).local().format('h:mma')
        }
    },
	components: {
		GroupMenu,
	},
}
</script>