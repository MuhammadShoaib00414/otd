<template>
    <div>
        <div class="nav-container custom-navbar" style="z-index: 1000;">
            <div>
                <div class="container-fluid">
                    <nav class="navbar navbar-expand-lg px-0 py-0 d-flex justify-content-between">
                        <router-link :to="'/'" aria-label="home" id="logoContainer" class="navbar-brand" href="/" style="height: 3.5em;">
                            <img aria-hidden="true" :src="'https://'+vWindow.location.hostname + '/logo'" style="height: 100%;">
                        </router-link>
                        <a id="notificationBell" style="text-decoration: none;" href="/notifications" class="mr-3 mv-show d-none">
                                <svg aria-hidden="true" width="22px" height="22px" viewBox="0 0 16 16" class="bi bi-bell-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
                                </svg>
                            <span v-if="this.notificationCount > 0" id="unreadNotificationCount" aria-hidden="true" style="vertical-align: top; font-size:0.7em; position:absolute; transform: translateX(-17%); height: 8px; width: 8px; border-radius: 50%; display: inline-block;" class="badge badge-danger">
                            </span>
                         </a>
                        <div class="d-flex">
                            <form method="GET" action="/search" class="mr-3 d-none d-md-flex align-items-center">
                                <i class="icon icon-magnifying-glass mx-2" style="position: absolute; cursor: pointer; z-index: 5; font-size: 1.2em; opacity: 0.6"></i>
                                <input type="search" placeholder="Search" class="form-control" name="q" style="min-width: 300px; background-color: rgba(255, 255, 255, 0.4); padding-left: 35px; border: 1px solid rgba(250, 250, 250, 0.4); z-index: 1; border-radius: 10px;" require id="searchInput">
                            </form>
                            <button class="navbar-toggler py-0" id="showMobileMenu" type="button" @click="showMobileMenu = true;">
                                <i class="fas fa-bars fa-lg" style="height: 100%;"></i>
                            </button>
                            <div v-if="$settings.dashboard_tutorial && $settings.dashboard_tutorial.url != ''" class="d-none d-md-flex align-items-center">
                                <a :href="$settings.dashboard_tutorial.url" target="_blank" style="display: block;" class="mr-2">
                                    <question-mark class="question-mark" aria-hidden="true" style="height: 25px; width: 25px; display: block;"></question-mark>
                                </a>
                            </div>
                            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                                <a id="notificationBell" style="text-decoration: none;" href="/notifications" class="mr-3">
                                    <svg aria-hidden="true" width="22px" height="22px" viewBox="0 0 16 16" class="bi bi-bell-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
                                    </svg>
                                    <span id="unreadNotificationCount" aria-hidden="true" style="display: inline-block; vertical-align: top; font-size:0.7em; transform: translateX(-45%); height: 9px; width: 5px; border-radius: 50%;" class="badge badge-danger" :class="{ 'd-none': notificationCount == 0 }"></span>
                                </a>
                                <ul class="navbar-nav" role="presentation">
                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/messages">{{ $__.messages['messages'] }}</a></li>

                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/introductions">
                                            {{ $__.messages['introductions'] }}
                                            <span v-if="$user.unread_introduction_count > 0" class="badge badge-danger">{{ $user.unread_introduction_count }}</span>
                                        </a>
                                    </li>

                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/account">{{ $__.messages['account'] }}</a></li>

                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" :href="'/users/'+$user.id">{{ $__.messages['profile'] }}</a></li>

                                    <li v-if="$settings.is_stripe_enabled && $user.receipts_count" class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/purchases">{{ $__.messages['purchases'] }}</a></li>
                                    <li class="nav-item d-block d-lg-none text-right"><a class="nav-link" href="/logout">{{ $__.messages['logout'] }}</a></li>

                                    <li class="d-none d-lg-block nav-item dropdown">
                                        <a class="nav-link dropdown-toggle dropdown-toggle p-lg-0" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span>{{ $user.name }}</span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right" aria-labelledby="dropdown01" style="z-index: 100000;">
                                            <a class="dropdown-item" href="/messages">
                                                {{ $__.messages['messages'] }}
                                                <span v-if="$user.unread_message_count > 0" class="badge badge-danger">{{ $user.unread_message_count }}</span>
                                            </a>
                                            
                                            <a class="dropdown-item" href="/browse">{{ $settings.find_your_people_alias }}</a>


                                            <a  v-if="this.base_url != 'https://todayisagoodday.onthedotglobal.com' "  class="dropdown-item" href="/introductions">{{ $__.messages['introductions'] }}
                                                <span v-if="$user.unread_introduction_count > 0" class="badge badge-danger">{{ $user.unread_introduction_count }}</span>
                                            </a>
                                            <a  v-if="this.base_url != 'https://todayisagoodday.onthedotglobal.com' "  class="dropdown-item" href="/shoutouts/received">
                                                {{ $__.messages['shoutouts'] }}
                                                <span v-if="$user.unread_shoutout_count > 0" class="badge badge-danger">{{ $user.unread_shoutout_count }}</span>
                                            </a>

                                            
                                            <a class="dropdown-item" href="/ideations" v-if="$settings.is_ideations_enabled">
                                                {{ $__.messages['ideations'] }}
                                                <span v-if="$user.unread_ideation_invitations_count" class="badge badge-danger">{{ $user.unread_ideation_invitations_count }}</span>
                                            </a>
                                            <a v-if="$settings.is_stripe_enabled && $user.receipts_count" class="dropdown-item" href="/purchases">{{ $__.messages['purchases'] }}</a>
                                            <a class="dropdown-item" href="/account">{{ $__.messages['account'] }}</a>
                                            <a class="dropdown-item" :href="'/users/'+$user.id">{{ $__.messages['profile'] }}</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="/logout">{{ $__.messages['logout'] }}</a>
                                        </div>
                                    </li>
                                </ul>
                            </div> 
                        </div>
                      
                    </nav>
                </div>

            </div>
        </div>
        <div id="mobileMenu" v-show="showMobileMenu" @click="showMobileMenu = false;" class="bg-primary-100 text-primary-700 font-black" style="z-index: 1000000; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
            <div style="height: 100%; width: 100%; overflow-y: scroll;">
                <span class="font-weight-bold text-primary-700 mb-0 mt-2 ml-2">{{ $__.messages['menu'] }}</span>
                <a href="#" class="px-2" id="closeMenuButton" style="color: #000; position: fixed; top: 0; right: 0.25em; font-size: 32px; font-weight: bold;">&times;</a>
                <div class="mt-1 ml-2">
                    <dashboard-menu :groups="$usersGroups"></dashboard-menu>
                </div>
                <div class="ml-2 mb-4">
                    <hr>
                    <a href="/logout">{{ $__.messages['logout'] }}</a>
                </div>
            </div>
        </div>
        <br>
    </div>
</template>

<script>
    import DashboardMenu from './DashboardMenu'
    import bus from '../../bus'
    import QuestionMark from './QuestionMark'

    export default {
        data() {
            return {
                vWindow: window,
                showMobileMenu: false,
                notificationCount: 0,
                base_url: '',
            }
        },
        computed: {
            hasUserBeenLoaded() {
                return this.$user;
            }
        },
        methods: {
            loadNotificationCount() {
                if (document.hidden) return;

                axios.get('/api/notifications')
                     .then((response) => {
                        this.notificationCount = response.data.hasUnreadNotifications;
                     })
                     console.log('cout.',this.notificationCount);
            },
            loopLoadNotifications() {
                this.loadNotificationCount();
                if (document.head.querySelector('meta[name="prevent_looping_notifications"]') == null) {
                    setTimeout(() => {
                        this.loopLoadNotifications();
                    }, 6000)
                }
            }
        },
        components: {
            DashboardMenu,
            QuestionMark,
        },
        created() {
            this.loopLoadNotifications();
            if(/iPhone/i.test(navigator.userAgent)){
                $('#unreadNotificationCount').css('top','16px');
                $('#unreadNotificationCount').css('right','57px');
            }
            axios.get('/api/settings')
			.then((response) => {
            this.base_url = response.data.base_url;
           
           
			})
			.catch((error) => {
				this.settings = false;
			});
        },
    }
</script>