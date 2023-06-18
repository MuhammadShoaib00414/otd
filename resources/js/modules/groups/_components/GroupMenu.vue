<template>
    <div class="nav flex-column mb-2">
        <router-link class="nav-link" :class="{ 'font-weight-bold': true }" :to="'/groups/'+group.slug">
            <i class="icon-home mr-1"></i>
            <span v-if="group.home_page_name">{{ group.home_page_name }}</span>
            <span v-else>Group Home</span>
        </router-link>
        <a class="nav-link" :href="'/groups/' + group.slug + '/posts'" v-if="group.is_posts_enabled == 1">
            <i class="icon-archive mr-1"></i>
            <span v-if="group.posts_page_name">{{ group.posts_page_name }}</span>
            <span v-else>Posts</span>
        </a>
        <a class="nav-link" :href="'/groups/' + group.slug + '/members'">
            <i class="icon-users mr-1"></i>
            <span v-if="group.members_page_name">{{ group.members_page_name }}</span>
            <span v-if="!group.members_page_name">Members</span>
        </a>
        <a class="nav-link" :href="'/groups/' + group.slug + '/content'" v-if="group.is_content_enabled">
            <i class="icon-text-document-inverted mr-1"></i>
            <span v-if="group.content_page_name">{{ group.content_page_name }}</span>
            <span v-else>Content</span>
        </a>
        <a class="nav-link" :href="'/groups/' + group.slug + '/calendar'" v-if="group.is_events_enabled">
            <i class="icon-calendar mr-1"></i>
            <span v-if="group.calendar_page_name">{{ group.calendar_page_name }}</span>
            <span v-else>Calendar</span>
        </a>
        <a class="nav-link" :href="'/groups/' + group.slug + '/shoutouts'" v-if="group.is_shoutouts_enabled">
            <i class="icon-megaphone mr-1"></i>
            <span v-if="group.shoutouts_page_name">{{ group.shoutouts_page_name }}</span>
            <span v-else>Shoutouts</span>
        </a>
        <router-link class="nav-link" :to="'/groups/' + group.slug + '/discussions'"
            v-if="group.is_discussions_enabled">
            <i class="icon-chat mr-1"></i>
            <span v-if="group.discussions_page_name">{{ group.discussions_page_name }}</span>
            <span v-else>Discussions</span>
        </router-link>
        <a dusk="files" class="nav-link" :href="'/groups/' + group.slug + '/files'" v-if="group.is_files_enabled">
            <i class="icon-folder mr-1"></i>
            <span v-if="group.files_alias">{{ group.files_alias }}</span>
            <span v-else>Files</span>
        </a>
        <a v-if="group.has_subgroups" class="nav-link" :href="'/groups/' + group.slug + '/subgroups'">
            <i class="icon-database mr-1"></i>
            <span v-if="group.subgroups_page_name">{{ group.subgroups_page_name }}</span>
            <span v-else>Subgroups</span>
        </a>
        <a class="nav-link" :href="'/groups/' + group.slug + '/sequence'"
            v-if="$settings.is_sequence_enabled && group.sequence_count && group.is_sequence_enabled">
            <i class="fas fa-book mr-1"></i> {{ group.sequence.name }}
        </a>
        <div v-if="isUserAdminOfGroup || $user.is_admin">
            <a class="nav-link" :href="'/groups/' + group.slug + '/budgets'"
                v-if="group.budgets_count && group.is_budgets_enabled"><i class="icon-credit-card mr-1"></i> {{
                        $__.general['budgets']
                }}</a>
            <a class="nav-link" :href="'/groups/' + group.slug + '/reports/demographics'"
                v-if="group.is_reporting_enabled"><i class="icon-pie-chart mr-1"></i>{{ $__.messages['reports'] }}</a>
            <a class="nav-link" :href="'/groups/' + group.slug + '/email-campaigns'"
                v-if="group.is_email_campaigns_enabled"><i class="icon-mail mr-1"></i> {{
                        $__.messages['email-campaigns']
                }}</a>
            <a class="nav-link" :href="'/groups/' + group.slug + '/activity'" v-if="group.is_reporting_enabled"><i
                    class="fa fa-history mr-1"></i> {{ $__.messages['activity'] }}</a>
            <a class="nav-link" :href="'/groups/' + group.slug + '/edit'"><i class="icon-cog mr-1"></i> {{
                    $__.messages['settings']
            }}</a>
        </div>
        <div>

            <div v-if="!pages">
                <page-skeleton></page-skeleton>
            </div>
            <div class="mt-3" v-if="settings" v-else>
                <div class="mb-3">
                    <h5 class="text-uppercase text-muted mb-1 mt-1" style="font-size: 14px;">{{
                            settings
                    }}</h5>
                    <div class="nav flex-column" v-for="page in pages" :key="page.id">
                        <a v-if="page.is_active == 1 &&  page.displayed_show != '' "
                            class="d-block mb-1 cursor-pointer"
                            @click="showpop(page.id, '/pages/' + page.id + '/' + page.slug, this)">
                            {{ page.title }}</span></a>
                    </div>
                    <span>
                        <a v-if="pages.length >= 5" id="slug" class="p-1 hover:text-primary-800 badge"
                            :href="'pages/' + settings.replace(/\s+/g, '-').toLowerCase()+'?group=' + this.$route.params.slug ">
                            Load more..</a>
                    </span>
                </div>
            </div>
        </div>
        <div class="mt-3" v-if="group.custom_menu">
            <div class="mb-3" v-for="menuGroup in custom_menu_groups">
                <p class="mb-1"><b>{{ menuGroup.title }}</b></p>
                <div class="nav flex-column">
                    <a v-for="link in menuGroup.links" class="nav-link" :href="link.url" :target="link.target">{{
                            link.title
                    }}</a>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import PageSkeleton from './../../skeleton/PageSkeleton';
import PagesModal from '../../_components/PagesModal'
export default {
    props: ['isUserAdminOfGroup','groups'],
    data() {
        return {
            pages:[],
            custom_menu_groups: [],
            group: {
                name: '',
                slug: '',
                id: null,
            },
            showpopModel: false,
        }
    },
    components: {
        PageSkeleton,
        PagesModal
    },
    created() {
        axios.get('/api/groups/' + this.$route.params.slug)
            .then((response) => {
                this.group = response.data;
                if (this.group.custom_menu)
                    this.custom_menu_groups = JSON.parse(this.group.custom_menu).groups;
                this.custom_menu_groups.forEach((menuGroup) => {
                    menuGroup.links.forEach((link) => {
                        if (link.url.toLowerCase().includes(window.location.hostname.toLowerCase()))
                            link.target = "_self"
                        else
                            link.target = "_blank"
                    })
                })
                // this.haveGroupsLoaded = true;
            });
        axios.get('/api/pages/' + this.$route.params.slug)
            .then((response) => {
                // if key exists in response
                if (response.data.hasOwnProperty('pages')) {
                    this.pages = response.data.pages;
                    this.settings = response.data.settings.value;
                } else {
                    this.pages = true;
					this.settings = false;
                }
                console.log(this.pages, this.settings)
            })
            .catch((error) => {
                console.log('pages error', error);
            });
    },
    methods: {
        showpop(id, obj_url, obj) {
			$('#pop-up').html('');
			var iframe = '<iframe src="' + obj_url + '"  style="background: #fff;height: 75vh!important;width: 75vw !important;"></iframe>';
			$('#modal_content').modal('show');
			$('#pop-up').html(iframe);
		},
    }
}
</script>