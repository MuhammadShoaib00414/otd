<template>
   
    <div>
        <div class="container-fluid pt-3" v-show="confirmUserIsNotEventOnly">
            <div v-if="haveHeaderImagesLoaded && header_image" class="group-header-bg" :style="'background-image: url(\''+header_image+'\'); background-size: cover; background-position: center center;'">
                <div class="group-header-sizer-1101"></div>
            </div>
            <div v-if="haveHeaderImagesLoaded && desktop_room" :class="{ 'd-none d-md-block': mobile_room }">
                <div style="width: 100%; position: relative; text-align: center;">
                    <img :src="desktop_room.image_url" style="width: 100%">
                    <div v-for="area in desktop_room.click_areas" :style="'text-align: left; position: absolute; top: '+ area.y_coor+'; left: '+area.x_coor+'; height:'+area.height+'; width:'+area.width+'; z-index: 100;'">
                        <a :href="area.target_url" :target="area.a_target" style="position: absolute; height: 100%; width: 100%"></a>
                    </div>
                </div>
            </div>
            <div v-if="haveHeaderImagesLoaded && mobile_room" class="d-block d-md-none">
                <div style="width: 100%; position: relative; text-align: center;">
                    <img :src="mobile_room.image_url" style="width: 100%">
                    <div v-for="area in mobile_room.click_areas" :style="'text-align: left; position: absolute; top: '+ area.y_coor+'; left: '+area.x_coor+'; height:'+area.height+'; width:'+area.width+'; z-index: 100;'">
                        <a :href="area.target_url" :target="area.a_target" style="position: absolute; height: 100%; width: 100%"></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-3 d-none d-md-block">

                    <div v-if="$settings.dashboard_left_nav_image">

                        <a v-if="$settings.dashboard_left_nav_image" :href="$settings.dashboard_left_nav_image_link" :target="($settings.does_dashboard_left_nav_image_open_new_tab) ? '_blank' : ''">
                            <img class="mb-2" style="width: 100%" :src="$settings.dashboard_left_nav_image">
                        </a>
                        <img v-else class="mb-2" style="width: 100%" :src="$settings.dashboard_left_nav_image">

                    </div>

                    <dashboard-menu :groups="groups" :dashboardNotifications="dashboardNotifications"></dashboard-menu>
                    
                </div>
                <div class="col-12 col-md-6">
                    <feed :type="'dashboard'"></feed>
                </div>
                <div class="col-3 d-none d-md-block">
                    <div v-if="$settings.is_points_enabled" class="text-center pb-3 my-2" style="border-bottom: 1px solid hsla(220, 25%, 85%, 1)">
                        <span class="font-secondary-brand d-block" style="line-height: 1; font-size: 48px; font-weight: 500;">{{ $user.points_ytd }}</span>
                        <a href="/my-points"><span class="d-block">{{ $__.messages["home.points"] }}</span></a>
                    </div>
                    <div class="bg-color pb-3">
                        <div class="d-flex flex-wrap justify-content-around cursor-pointer">
                            
                            <div v-for="badge in $user.badges" class="mx-2 my-1 text-center badge-circle share-wrapper" style="text-align: center;"  :title="badge.name ? badge.name : badge.name" >
                                <img v-if="badge.icon && badge.icon != '/'" :src="badge.icon.slice(1)" style="height: 45px;">
                                <badge1 v-else-if="badge.id == 1"></badge1>
                                <badge2 v-else-if="badge.id == 2"></badge2>
                                <badge3 v-else-if="badge.id == 3"></badge3>
                                <badge4 v-else-if="badge.id == 4"></badge4>
                                <badge5 v-else-if="badge.id == 5"></badge5>
                                <default-badge v-else-if="badge.id > 5 && !badge.icon"></default-badge>
                
                            </div>
                            <div v-if="($usersGroups.length >= 3 && $user.badges_groups)" class="mx-2 my-1 badge-circle" style="text-transform: capitalize;text-align: center;"   :title="toTitleCase($user.badges_groups.name)" >
                                <img :src="$user.badges_groups.icon.slice(1)" style="height: 45px;" />
                            </div> 
                           
 
        
                        </div>
                       
                    </div>
           
                    <div>
                        <div class="bg-light-secondary-brand py-2 px-2 mb-2">
                            <p class="font-weight-bold">{{ $__.messages['home.people'] }}</p>
                        </div>
                        <div v-if="isUsersLoading">
                            <user-skeleton v-for="i in 12" :key="i"></user-skeleton>
                        </div>
                        <div v-else>
                            <div v-for="(category, index) in peopleYouShouldKnowCategories" :key="index" class="mb-2">
                                <h6 style="text-transform: uppercase;" class="mb-1">{{ category.name }}</h6>
                                <a v-for="(user, uIndex) in category.users" :key="uIndex" :href="'/users/'+user.id" class="card mb-2 px-1 no-underline">
                                  <div class="card-body p-1">
                                    <div class="ml-1 d-flex align-items-center">
                                      <div :style="'height: 3em; width: 3em; border-radius: 50%; background-image: url(\''+user.photo_url+'\'); background-size: cover; background-position: center; flex-shrink: 0;'">
                                      </div>
                                      <div class="ml-3">
                                        <span class="d-block mb-1" style="font-size: 0.85em; color: #343a40; font-weight: 600;">{{ user.name }}</span>
                                        <span class="d-block card-subtitle mb-1 text-muted" style="font-size: 0.85em; line-height: 1.2;">{{ user.job_title }}</span>
                                      </div>
                                    </div>
                                  </div>
                                </a>
                                <div class="text-center">
                                  <a :href="'/browse/?options[0]='+category.id">{{ $__.messages['browse'] }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'


    import Feed from './posts/Feed'
    import DashboardMenu from './_components/DashboardMenu'
    import LatestArticles from './_components/LatestArticles'
    import Badge1 from './_components/badges/badge1'
    import Badge2 from './_components/badges/badge2'
    import Badge3 from './_components/badges/badge3'
    import Badge4 from './_components/badges/badge4'
    import Badge5 from './_components/badges/badge5'
    import DefaultBadge from './_components/badges/BadgeDefault'
    import UserSkeleton from './skeleton/UserCardSkeleton';

    export default {
        data() {
            return {
           
                groups: [],
                isUsersLoading: true,
                peopleYouShouldKnowCategories: [],
                haveHeaderImagesLoaded: false,
                confirmUserIsNotEventOnly: false,
                dashboardNotifications: {
                    unread_message_count: 0,
                    has_ideation_notifications: 0,
                    has_introduction_notifications: 0,
                    has_shoutout_notifications: 0,
                    has_event_notifications: 0,
                    unread_introduction_count: 0,
                    unread_shoutout_count: 0,
                    unread_ideation_invitations_count: 0},
            }
        },
        created() {
            var url = window.location.href;
            if(url.includes("invite") == true || this.$user.is_onboarded == 0) {
                window.location.href = '/onboarding';
            }else if(this.$user.is_event_only == 1){
                window.location.href = '/my-groups';
            }else{
                this.confirmUserIsNotEventOnly = true;
            this.groups = this.$usersGroups;
            var requests = [axios.get('/api/user/people-you-should-know'), axios.get('/api/dashboardHeader?v='+Date.now()), axios.get('/api/dashboard-notifications')];
            axios.all(requests).then(axios.spread((response1, response2, response3) => {
                this.peopleYouShouldKnowCategories = response1.data;
                this.header_image = response2.data.header_image;
                this.desktop_room = response2.data.desktop_virtual_room;
                this.mobile_room = response2.data.mobile_virtual_room;
                this.haveHeaderImagesLoaded = true;
                this.dashboardNotifications = response3.data;
                this.isUsersLoading = false;
            }));
          }
        },
        
        components: {
            Feed,
            DashboardMenu,
            LatestArticles,
            Badge1,
            Badge2,
            Badge3,
            Badge4,
            Badge5,
            DefaultBadge,
            UserSkeleton
        },

        filters: {
            first10Chars(string) {
                if(string.length > 10)
                    string = string.substring(0,10) + "...";
                return string;
            }
        },
        methods:{
             toTitleCase(str) {
            return str.toLowerCase().split(' ').map(function (word) {
                return (word.charAt(0).toUpperCase() + word.slice(1));
            }).join(' ');
            },
          
    },
        
    }
</script>
