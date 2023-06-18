<template>
  <div class="card-body d-flex justify-content-between pb-2" style="border-bottom: 1px solid #e9ecef;">
      <post-actions :post="post" v-on="$listeners" :type="type" :canBeMovedUp="canBeMovedUp"></post-actions>
      <reported :post="post"></reported>
      <div>
   
        <a v-if="post.posted_as_group_id" class="text-primary-400" :href="'/groups/' + post.posted_by_group.slug"><b>{{ post.posted_by_group.name }}</b></a>
        <b v-if="!post.posted_as_group_id && !post.post.user">{{ $settings.name }}</b>

        <div v-else="post.post.user" class="row">
          <a :href="'/users/'+ post.post.user.id" class="d-flex no-underline font-dark">
            <div  v-if="!post.posted_as_group_id" class="mr-2" :style="'height: 2.25em; width: 2.25em; min-height: 32px; min-width: 32px; border-radius: 50%; background: url(\'' + post.post.user.photo_path +'\'); background-size: cover; background-position: center;'">
            </div>
            <div>
              <span class="d-block font-weight-bold" style="line-height: 1;" v-if="!post.posted_as_group_id">{{ post.post.user.name }}
                  <span v-if="post.is_poster_group_admin" class="badge ml-1 bg-primary-100">{{ $__.groups['Group Admin'] }}</span>
              </span>
              <span style="line-height: 1;" v-if="!post.posted_as_group_id">{{ post.post.user.job_title }}</span>
            </div>
          </a>
        </div>
      </div>

      <div class="text-right" style="line-height: 1.3;">
        {{ post.post_at | humanReadableDate }}
        <br>
        {{ post.post_at | humanReadableTime }}
      </div>
    </div>

</template>

<script>
  import moment from 'moment'
  import Reported from './Reported'
  import PostActions from './PostActions'

  export default {
      props: ['post', 'type', 'canBeMovedUp'],
      components: {
          Reported,
          PostActions,
      },
      data() {
          return {}
      },
      created(){
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
