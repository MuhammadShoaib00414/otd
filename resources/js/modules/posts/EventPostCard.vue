<template>
    <div class="card">
        <post-header :post="post" :type="post.post_type" :canBeMovedUp="canBeMovedUp"></post-header>
        <div class="card-body">
            <div class="row justify-content-center align-items-center mb-1">
                <div v-if="isLive" class="badge badge-danger pr-1 pl-1 mr-2">
                    {{ $__.events['LIVE'] }}
                </div>
                <h5 class="font-secondary-brand font-weight-bold" style="font-size: 1em;">
                    <span v-if="isLive">{{ $__.events['Live Event'] }}</span>
                    <span v-if="!hasHappened && !isLive && !isCancelled">{{ $__.events['Upcoming Event'] }}</span>
                    <span v-if="hasHappened && !isLive && !isCancelled">{{ $__.events['Past Event'] }}</span>
                    <span v-if="isCancelled && !isLive">{{ $__.events['Cancelled Event'] }}</span>
                </h5>
            </div>

            <img :src="post.post.image_url" style="max-width: 100%; width: 100%">

            <p class="font-weight-bold">{{ post.post.name }}</p>
            <p>{{ post.post.date | humanReadableDate }} {{ post.post.date | humanReadableTime }} <small class="text-muted">({{ usersTimezone }})</small></p>
        
            <div class="d-flex justify-content-center flex-wrap mb-2">
                <a :href="'/groups/'+post.group.slug+'/events/'+post.post.id" class="mb-1 d-block btn btn-outline-primary">{{ $__.events['Event Details'] }}</a>
                <a v-for="link in post.post.custom_menu" style="white-space: normal;" target="_blank" :href="link.url" class="mb-1 d-block btn btn-light postLinkButton px-3 mx-1">
                    <span class="font-size-sm-sm" style="color:#3e4e63;">{{ link.title }} <span class="sr-only"> for {{ post.post.name }}</span></span>
                </a>
            </div>
        </div>

        <post-footer :post="post"></post-footer>
    </div>
</template>

<script>
    import moment from 'moment'
    import PostFooter from './_components/PostFooter'
    import PostHeader from './_components/PostHeader'

    export default {
        props: ['post', 'canBeMovedUp'],
        data() {
            return {
                usersTimezone: '',
            }
        },
        computed: {
            hasHappened() {
                return moment(this.post.post.end_date).isBefore();
            },
            isCancelled() {
                return this.post.post.is_cancelled;
            },
            isLive() {
                return moment(this.post.post.date).isBefore() && moment(this.post.post.end_date).isAfter();
            }
        },
        components: {
            PostFooter,
            PostHeader,
        },
        mounted() {
            this.usersTimezone = this.$user.timezone;
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
    }
</script>