<template>
    <div v-if="post.group || $settings.is_likes_enabled" class="card-footer text-muted py-0" style="position: relative; background-color: hsla(220, 18%, 98%, 1); min-height: 40px;">
        <div class="d-flex align-items-center footer-content">
            <div class="text-center">
                <like-button v-if="$settings.is_likes_enabled" :post="post"></like-button>
            </div>
            <div class="text-center d-flex align-items-center ml-1" v-if="post.post_type != 'App\\DiscussionThread' && post.post_type != 'App\\DiscussionPost'" @click="toggleCommentSection()">
        		<i class="likeButton text-dark icon-message"></i> 
                <span style="font-size: 0.8em;cursor: pointer;margin-left: 6px;font-weight:500;">{{ post.comments_count }}</span>
            </div>
            <div class="ml-auto text-center">
                <div v-if="post.group && userIsMemberOfGroup(post.group.id)" >
                    <router-link :to="'/groups/'+post.group.slug" class="mx-auto">
                        <small>{{ post.group.name }}</small>
                    </router-link>
                </div>
                <div v-if="post.group && !userIsMemberOfGroup(post.group.id)" >
                    <small>{{ post.group.name }}</small>
                </div>
            </div>
            <span v-if="post.is_pinned" class="badge badge-secondary pinnedBadge ml-2" style=""> <i class="icon-pin"></i> {{ $__.posts['pinned'] }} </span>
        </div>
        <post-comments :post="post" v-if="commentToggle"></post-comments>
    </div>
    
</template>


<script>
    import LikeButton from './PostLikeButton'
    import PostComments from './PostComments'

     export default {
        props: ['post'],
        data() {
            return {
                isNight: true,
                toggle: true,
                commentToggle: false
            }
        },
        created(){
            localStorage.clear();
        },
        methods: {
            userIsMemberOfGroup(groupId) {
                var areThey = false;
                this.$usersGroups.forEach((group) => {
                    if (group.id == groupId)
                        areThey = true;
                })
                return areThey;
            },
            toggleCommentSection() {
                this.commentToggle = !this.commentToggle;
            }
        },
        components: {
            LikeButton,
            PostComments
        }
     }
</script>