
<style>
.group-header-sizer-1101 {
    width: 100%;
    padding-top: 23%;
}

.chat-container {
    padding: 1em;
    position: absolute;
    height: 100%;
    width: 100%;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
}

@media (max-width: 800px) {
    .chat-container {
        position: relative;
        max-height: 400px;
    }
}
</style>

<template>
    <div>
        <div v-if="group.header_bg_image_path && !group.desktop_room" class="group-header-bg"
            :style="'background-image: url(\'' + group.header_bg_image_url + '\'); background-size: cover; position: relative;width: 100%;background-size: contain;background-repeat: no-repeat;'">
            <div class="group-header-sizer-1101"></div>
            <div class="chat-container d-sm-block">
                <live-chat :room="group.chat_room" type="floating"
                    v-if="group.chat_room && group.chat_room.is_enabled && chatbox == 1 && !group.should_live_chat_display_below_header_image"></live-chat>
            </div>
        </div>
        <div v-if="group.desktop_room" :class="{ 'd-none d-md-block': group.mobile_room }">
            <div style="width: 100%; position: relative; text-align: center;">
                <img :src="group.desktop_room.image_url" style="width: 100%">
                <div v-for="area in group.desktop_room.click_areas"
                    :style="'text-align: left; position: absolute; top: ' + area.y_coor + '; left: ' + area.x_coor + '; height:' + area.height + '; width:' + area.width + '; z-index: 100;'">
                    <a :href="area.target_url" :target="area.a_target"
                        style="position: absolute; height: 100%; width: 100%"></a>
                </div>
                <div class="chat-container d-none d-sm-block">

                    <live-chat :room="group.chat_room" type="floating"
                        v-if="group.chat_room && group.chat_room.is_enabled && chatbox == 1"></live-chat>
                </div>
            </div>
        </div>
        <div v-if="group.mobile_room" class="d-block d-md-none">
            <div style="width: 100%; position: relative; text-align: center;">
                <img :src="group.mobile_room.image_url" style="width: 100%">
                <div v-for="area in group.mobile_room.click_areas"
                    :style="'text-align: left; position: absolute; top: ' + area.y_coor + '; left: ' + area.x_coor + '; height:' + area.height + '; width:' + area.width + '; z-index: 100;'">
                    <a :href="area.target_url" :target="area.a_target"
                        style="position: absolute; height: 100%; width: 100%"></a>
                </div>
            </div>
        </div>

        <div class="container-fluid pt-3 mt-10">
            <div class="mb-2">

                <router-link v-if="!group.parent && !$user.is_event_only" to="/home">
                    < {{ $__.messages['Back to Dashboard'] }}</router-link>
                        <a v-else-if="!group.parent && $user.is_event_only" href="/my-groups">
                            < {{ $__.messages['Back to My Groups'] }}</a>
                                <div v-else-if="group.parent">
                                    <router-link v-if="!$user.is_event_only" to="/home">{{
                                        $__.messages['Dashboard']
                                    }}</router-link>
                                    <router-link v-if="!group.parent_group_id" :to="'/groups/' + group.parent.slug">> {{
                                        group.parent.name
                                    }}</router-link>
                                    <router-link v-if="group.parent_group_id" :to="'/groups/' + group.parent.slug"> > {{
                                        group.parent.name
                                    }} </router-link>
                                    <a v-if="group.parent_group_id" :href="'/groups/' + group.parent.slug + '/subgroups'">
                                        > Subgroups </a>
                                    <router-link v-if="group.parent_group_id" :to="'/groups/' + group.slug"> > {{
                                        group.name
                                    }}</router-link>
                                </div>

            </div>
            <div class="row">
                <div class="col-3 d-none d-md-block">
                    <div v-if="isLoadingGroup">
                        <group-list-skeleton></group-list-skeleton>
                    </div>
                    <group-menu :isUserAdminOfGroup="isUserAdminOfGroup" v-else></group-menu>

                    <div v-if="isLoadingGroup">
                        <question-skeleton></question-skeleton>
                    </div>
                    <div class="bg-light-brand p-3 mb-2" v-else>
                        <p id="banner_cta_title" class="font-weight-bold">{{ group.banner_cta_title }}</p>
                        <p id="banner_cta_paragraph">{{ group.banner_cta_paragraph }}</p>
                        <a id="banner_cta_button" :href="group.banner_cta_url" class="btn btn-primary px-1"
                            style="white-space: normal; max-width: 100%; overflow: hidden; font-size: 14px;">{{
                                group.banner_cta_button
                            }} <i class="icon-controller-play ml-1"></i></a>
                    </div>
                    <div v-if="isLoadingGroup">
                        <article-skeleton v-for="i in 5" :key="i" />
                    </div>
                    <latest-articles :type="'group'" :group="group.slug" v-if="group.id"></latest-articles>
                </div>
                <div class="col-12 col-md-6">
                    <div class="d-block d-md-none mb-2">
                        <button class="btn btn-primary w-100 mb-2" @click="showGroupMenu = !showGroupMenu">{{
                            $__.messages['Group Menu']
                        }}</button>
                        <group-menu class="mb-2" :group="group" :isUserAdminOfGroup="isUserAdminOfGroup"
                            v-show="showGroupMenu" @click="showGroupMenu = false"></group-menu>
                        <live-chat type="inline" :room="group.chat_room"
                            v-if="group.chat_room && group.chat_room.is_enabled"></live-chat>
                    </div>
                    <div v-if="group.is_sequence_visible_on_group_dashboard && group.sequence && $settings.is_sequence_enabled && group.is_sequence_enabled && group.sequence.modules.length"
                        id="carouselExampleControls" class="carousel slide mb-2" data-ride="carousel"
                        data-interval="false">
                        <div class="carousel-inner">
                            <a v-for="(learning_module, index) in group.sequence.modules"
                                :href="(learning_module.is_available) ? '/groups/' + group.slug + '/sequence/modules/' + learning_module.id : ''"
                                class="carousel-item"
                                :class="(index == group.sequence.last_available_index) ? ' active' : ''">
                                <img :src="learning_module.thumbnail_image_path" class="d-block w-100"
                                    :alt="learning_module.name"
                                    :style="learning_module.is_available ? '' : 'filter: grayscale(1)'">
                                <div v-if="learning_module.has_current_user_completed">
                                    <div
                                        style="position: absolute; top: 0; left: 0; height: 100%; width: 100%; border: 5px solid #26c126;">
                                    </div>
                                    <i class="fas fa-check-circle"
                                        style="color: #26c126; position: absolute; bottom: 2rem; right: 2rem; font-size: 3em;"></i>
                                </div>
                            </a>
                        </div>
                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                            data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">{{ $__.groups['Previous'] }}</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                            data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">{{ $__.groups['Next'] }}</span>
                        </a>
                    </div>
                    <div class="d-flex justify-content-between align-items-center my-3">
                        <h3 class="font-weight-bold mb-0">
                            {{ $__.messages['recent-activity'] }}
                        </h3>
                        <div v-if="showNewPostButton">
                            <a class="btn btn-sm btn-secondary px-2" :href="'/groups/' + group.slug + '/posts/select-type'">
                                {{ $__.posts['new_post'] }}
                            </a>
                        </div>
                    </div>
                    <feed class="mt-2" :group="$route.params.slug" :type="'group'"></feed>
                </div>
                <div class="col-3 d-none d-md-block">
                    <div v-if="!group.is_current_user_member">
                        <button class="btn btn-primary w-100" @click="joinGroup()">{{
                            $__.groups['Join group']
                        }}</button>
                    </div>
                    <div v-if="(isUserAdminOfGroup || $user.is_admin) && reportedPosts.length">
                        <a :href="'/groups/' + group.slug + '/flagged'">
                            <div class="bg-white p-2 mb-3 d-flex" style="border-top: 3px solid #d03232;">
                                <span class="badge badge-danger pb-1 pt-1 px-2">{{ reportedPosts.length }}</span>
                                <p class="font-weight-bold ml-1">{{ $__.groups['Reported Posts'] }}</p>
                            </div>
                        </a>
                    </div>

                    <live-chat :room="group.chat_room" type="inline"
                        v-if="group.chat_room && group.chat_room.is_enabled && group.should_live_chat_display_below_header_image"></live-chat>

                    <div v-if="group.upcoming_events && group.upcoming_events.length"
                        class="bg-light-secondary-brand pt-3 px-3 pb-2 mb-3 mt-2">
                        <b>Upcoming Events</b>
                        <a v-for="event in group.upcoming_events" :href="'/groups/' + group.slug + '/events/' + event.id"
                            class="d-flex justify-content-between my-2">
                            <div class="col pl-0">
                                <b>{{ event.name }}</b><br>
                                {{ event.date | humanReadableDateTime }}
                                <div v-if="currentDateTime(event.date ,event.end_date)" class="badge badge-danger pr-1 pl-1 mr-2">
                                {{ $__.events['LIVE'] }}
                            </div>
                            </div>
                        </a>
                    </div>

                    <div class="d-flex justify-content-between">
                        <p class="font-weight-bold mb-2">
                            <span v-if="group.members_page_name">
                                {{ group.members_page_name }}
                            </span>
                            <span v-else>
                                {{ $__.groups['Members'] }}
                            </span>
                        </p>
                        <a :href="'/groups/' + group.slug + '/members'" class="font-weight-bold" style="font-size: 14px;">{{
                            $__.messages['view-all']
                        }}</a>
                    </div>
                    <div v-if="isLoadingGroup">
                        <user-skeleton v-for="i in 12" :key="i"></user-skeleton>
                    </div>
                    <a v-for="(user, index) in group.random_active_users" :key="index" :href="'/users/' + user.id"
                        class="d-block card mx-0 mt-1 mb-2 px-1 no-underline">
                        <div class="card-body d-flex align-items-center justify-content-start p-0 py-1">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="mr-3 ml-2"
                                    :style="'height: 3em; width: 3em; border-radius: 50%; background-image: url(\'' + user.photo_path + '\'); background-size: cover; background-position: center; flex-shrink: 0;'">
                                </div>
                                <div class="pt-1">
                                    <span class="d-block" style="color: #343a40; font-weight: 600;">{{
                                        user.name
                                    }}</span>
                                    <span class="d-block card-subtitle mb-1 text-muted" style="margin-top: 0.005em;">{{
                                        user.job_title
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="text-center" v-if="!isLoadingGroup">
                        <a :href="'/groups/' + group.slug + '/members'">
                            All
                            <span v-if="group.members_page_name">
                                {{ group.members_page_name }}
                            </span>
                            <span v-else>
                                {{ $__.groups['Members'] }}
                            </span>
                            <i class="icon-chevron-small-right"></i>
                        </a>
                    </div>

                    <div v-if="group.embed_code" v-html="group.embed_code"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Feed from '../posts/Feed'
import GroupMenu from './_components/GroupMenu'
import LatestArticles from './../_components/LatestArticles'
import LiveChat from './../_components/LiveChat'
import UserSkeleton from './../skeleton/UserCardSkeleton'
import QuestionSkeleton from './../skeleton/QuestionSkeleton'
import GroupListSkeleton from './../skeleton/GroupsListSkeleton'
import ArticleSkeleton from './../skeleton/ArticleSkeleton'

import moment from 'moment'

export default {
    components: {
        Feed,
        GroupMenu,
        LatestArticles,
        LiveChat,
        UserSkeleton,
        QuestionSkeleton,
        GroupListSkeleton,
        ArticleSkeleton
    },
    data() {
        return {
            group: {
                name: '',
                slug: '',
                id: null,
            },
            isUserAdminOfGroup: false,
            reportedPosts: [],
            showGroupMenu: false,
            isLoadingGroup: true,
            chatbox: 0,
        }
    },
    computed: {
        showNewPostButton() {
            return true;
        },
       
    },
    methods: {
        getReportedPosts() {
            let context = this;
            axios.get('/api/groups/' + context.$route.params.slug + '/reported-posts')
                .then((response) => {
                    context.reportedPosts = response.data;
                })
        },
        joinGroup() {
            let context = this;
            if (!confirm('Are you sure you want to join this group?'))
                return;

            axios.post('/api/groups/' + context.$route.params.slug + '/join')
                .then((response) => {
                    context.group.is_current_user_member = true;
                });
        },
        loadGroup() {
            let context = this;
            if (localStorage.getItem('group.' + context.$route.params.slug))
                context.group = JSON.parse(localStorage.getItem('group.' + context.$route.params.slug));
            axios.get('/api/groups/' + context.$route.params.slug)
                .then((response) => {

                    context.group = response.data;
                    var current_date = moment(new Date()).local().format('M/D/YY h:mma');

                    var start_date = (context.group.chat_room) ? moment(context.group.chat_room.start_at).local().format('M/D/YY h:mma') : null;

                    var end_at = (context.group.chat_room) ? moment(context.group.chat_room.end_at).local().format('M/D/YY h:mma') : null;


                    if (current_date >= start_date && current_date <= end_at) {

                        context.chatbox = 1;
                    } else {
                        context.chatbox = 0;
                    }
                    if (context.group == false || !context.group) {
                        context.$router.push({ name: 'home' });
                        localStorage.setItem('redirect1');
                    }
                    var isMemberOfGroup = false;
                    context.$usersGroups.forEach((group) => {
                        if (group.id == context.group.id) {
                            if (group.pivot.is_admin)
                                context.isUserAdminOfGroup = true;
                            isMemberOfGroup = true;
                        }
                    });
                    if (!isMemberOfGroup && context.group.is_private && !context.$user.is_admin) {
                        context.$router.push({ name: 'home' });
                        localStorage.setItem('redirect2');
                    }
                    if (context.isUserAdminOfGroup || context.$user.is_admin)
                        context.getReportedPosts();
                    localStorage.setItem('group.' + context.$route.params.slug, JSON.stringify(response.data))
                    context.isLoadingGroup = false;
                });
        },
        currentDateTime(sDate,eDate) {
            var startDate = moment(sDate).local().format('M/D/YY h:mm a');
            var endDate = moment(eDate).local().format('M/D/YY h:mm a');
            return moment(startDate).isBefore() && moment(endDate).isAfter();
            // return moment().isBetween(startDate , endDate);
        },
    },
    created() {
        localStorage.clear();
        this.loadGroup();

    },
    beforeRouteUpdate(to, from, next) {
        next();
        this.loadGroup();
    },
    filters: {
        humanReadableDateTime: function (value) {
            if (!value) return ''
            return moment(value).local().format('M/D/YY h:mma')
        },
    },
}
</script>