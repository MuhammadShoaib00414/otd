<style>
    .commentBody > p > img {
        max-width: 100%;
    }
</style>

<template>
    <div class="card">
        <post-header :post="post" :type="type" :canBeMovedUp="canBeMovedUp"></post-header>

        <div class="card-body pb-2">
            <div>
                <div v-html="post.post.name"></div>
                <hr class="my-2">
                <div style="font-size: 14px;">
                    <p class="font-weight-bold mb-1">{{ $__.posts['Recent Replies'] }}</p>
                        <span id="ordermsg">
                            <div v-for="comment in post.post.recent_posts" class="ml-1 mb-2 discussion-post-body">
                                <p class="mb-0 font-weight-bold">{{ comment.user.name }}</p>
                                <div class="commentBody" v-html="comment.body"></div>
                            </div>
                     </span>

                </div>
                <hr class="mb-2">
                <div class="text-center" style="font-size: 14px;">
                  <router-link :to="'/groups/'+post.group.slug+'/discussions/'+post.post.slug">{{ $__.messages['View'] }}</router-link>
                  <span>&centerdot;</span>
                  <router-link :to="'/groups/'+post.group.slug+'/discussions/'+post.post.slug+'/#reply'">{{ $__.posts['Reply'] }}</router-link>
                </div>
            </div>
          </div>
          <post-footer :post="post"></post-footer>
    </div>
</template>

<script>
    import PostHeader from './_components/PostHeader'
    import PostFooter from './_components/PostFooter'
    export default {
        props: ['post', 'type', 'canBeMovedUp'],
        data() {
            return {}
        },
        components: {
            PostHeader,
            PostFooter
        }
    }
</script>