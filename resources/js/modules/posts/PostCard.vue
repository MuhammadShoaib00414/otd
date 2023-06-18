<template>
    <div>
        <shoutout-post v-if="post.post_type == 'App\\Shoutout'" :post="post" v-on="$listeners" :type="type" :canBeMovedUp="canBeMovedUp"></shoutout-post>
        <text-post v-if="post.post_type == 'App\\TextPost' || post.post_type == 'App\\DiscussionPost'" :post="post" v-on="$listeners" :type="type" :canBeMovedUp="canBeMovedUp"></text-post>
         <discussion-thread v-if="post.post_type == 'App\\DiscussionThread'" :post="post" v-on="$listeners" :type="type" :canBeMovedUp="canBeMovedUp"></discussion-thread> 
        <event-post v-if="post.post_type == 'App\\Event'" :post="post" v-on="$listeners" :canBeMovedUp="canBeMovedUp"></event-post>
        <article-post v-if="post.post_type == 'App\\ArticlePost'" :post="post" v-on="$listeners" :type="type" :canBeMovedUp="canBeMovedUp"></article-post> 
    </div>
</template>

<script>
    import ShoutoutPost from './ShoutoutPostCard'
    import TextPost from './TextPostCard'
    import DiscussionThread from './DiscussionThreadPostCard'
    import EventPost from './EventPostCard'
    import ArticlePost from './ArticlePost'
    import PostActions from './_components/PostActions'

    export default {
        props: ['post', 'type', 'canBeMovedUp'],
        data() {
            return {}
        },
        components: {
            ShoutoutPost,
            TextPost,
            DiscussionThread,
            EventPost,
            ArticlePost,
            PostActions,
        }
    }
</script>