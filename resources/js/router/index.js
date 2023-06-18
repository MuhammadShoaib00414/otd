
import Dashboard from './../modules/Dashboard'
import GroupsShow from './../modules/groups/GroupsShow'
import GroupsJoin from './../modules/groups/GroupsJoin'
import DiscussionsIndex from './../modules/groups/discussions/DiscussionsIndex'
import DiscussionShow from './../modules/groups/discussions/DiscussionShow'

export const routes = [
    {
        path: '/',
        component: Dashboard,
        name: 'home',
    }, {
        path: '/home',
        component: Dashboard,
        name: 'home1',
    }, {
        path: '/groups/join',
        component: GroupsJoin,
    }, {
        path: '/groups/:slug',
        component: GroupsShow,
    }, {
        path: '/groups/:slug/discussions/:discussionSlug',
        component: DiscussionShow,
    }, {
        path: '/groups/:slug/discussions',
        component: DiscussionsIndex,
    },
]