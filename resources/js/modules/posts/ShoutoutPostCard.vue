<template>
    <div class="card">
        <post-header :post="post" :type="type" :canBeMovedUp="canBeMovedUp"></post-header>

        <div class="card-body">
            <p>{{ $__.posts['Shoutout to'] }} <i class="icon-megaphone"></i></p> <!-- Needs to be updated to grab the groups default info here -->
            <a :href="'/users/'+post.post.shouted.id" class="d-block text-center mb-3 light-hover-bg font-dark py-2 mx-5">
              <div class="mb-2 mx-auto" :style="'height: 5.25em; width: 5.25em; border-radius: 50%;  min-height: 32px; min-width: 32px; background-image: url(\''+post.post.shouted.photo_path+'\'); background-size: cover; background-position: center;'">
              </div>
              <div class="text-center">
                <span class="d-block font-weight-bold" style="line-height: 1;">{{ post.post.shouted.name }}</span>
                <span style="line-height: 1;">{{ post.post.shouted.job_title }}</span>
              </div>
            </a>
            <div v-if="post.post.shouting" class="text-center mb-3">
              <a :href="'/messages/new?user='+post.post.shouted.id+'&message=RE Shoutout from '+post.post.shouting.name+', Congratulations'" class=" btn btn-sm btn-primary">{{ $__.messages['Say congrats'] }}</a>
            </div>
            <p>{{ post.post.body }}</p>
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