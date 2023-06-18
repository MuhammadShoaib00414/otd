<template>
    <div class="card mb-2">

      <post-header :post="post" :type="type" :canBeMovedUp="canBeMovedUp"></post-header>

      <div class="card-body">
        <div>
          <div class="pt-3 p-1 redactor-output" v-html="post.post.content"></div>
          <div class="d-flex justify-content-center flex-wrap mb-2" v-if="post.post.custom_menu">
              <a v-for="link in post.post.custom_menu" style="white-space: normal;" target="_blank" :href="link.url" class="mb-1 d-block btn btn-light postLinkButton px-3 mx-1">
                <span style="color:#3e4e63;">
                  {{ link.title }}
                  <span class="sr-only"> for {{ post.post.name }}</span>
                </span>
              </a>
          </div>
        </div>
      </div>

      <post-footer :post="post"></post-footer>
  </div>
</template>

<script>
  import moment from 'moment'
  import PostHeader from './_components/PostHeader'
  import PostFooter from './_components/PostFooter'
  import PostComments from './_components/PostComments'

    export default {
        props: ['post', 'type', 'canBeMovedUp'],
        data() {
            return {};
        },
        created() {},
        components: {
          PostHeader,
          PostFooter,
          PostComments
        }
    }
</script>