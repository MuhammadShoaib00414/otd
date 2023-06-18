<template>
    <div class="card" style="position: relative;">
        <post-header :post="post" :type="type" :canBeMovedUp="canBeMovedUp"></post-header>
        <post-actions :post="post" v-on="$listeners" :type="type" :canBeMovedUp="canBeMovedUp"></post-actions>
        <reported :post="post"></reported>
        <a :href="post.post.url">
            <img v-if="!post.post.is_video" :src="post.post.image" class="card-img-top" style="max-height: 22em; width: auto; width: 100%;">
            <iframe v-if="post.post.is_video" :src="post.post.embedded_video" style="height: 22em; width: 100%;"></iframe>
            <div class="card-body">
                <h5 class="card-title">{{ post.post.title }}</h5>
            </div>
        </a>

        <post-footer :post="post"></post-footer>
    </div>
</template>

<script>
    import PostHeader from './_components/PostHeader'
    import PostFooter from './_components/PostFooter'
    import PostActions from './_components/PostActions'
    import Reported from './_components/Reported'

    export default {
        props: ['post', 'type', 'canBeMovedUp'],
        data() {
            return {}
        },
        components: {
            PostHeader,
            PostFooter,
            PostActions,
            Reported,
        }
    }
</script>