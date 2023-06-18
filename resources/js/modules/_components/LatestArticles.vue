<template>
    <div>
        <div class="pt-2 d-none d-sm-block">
          <p class="font-weight-bold">{{ $__.general['latest-articles'] }}</p>
            <div v-if="isLoadingArticle">
                <article-skeleton v-for="i in 5" :key="i" />
            </div>

            <div v-for="(article, index) in articles" :key="index"  class="mb-3">
                <a :href="article.post.url" class="no-underline" :style="'background-color: #f7d0c9; background-image: url(\''+article.post.image_url+'\'); background-size: cover; background-position: center; height: 12em; display: flex; align-items: flex-end;'" target="_blank">
                  <span class="d-block px-2 py-1 mb-2 border-primary" style="background-color: #fff; width: 85%; color: #1e1e20; transform: translateX(-5px); border-left-width: 5px; border-left-style: solid;">
                  {{ str_limit(article.post.title) }}
                </span>
              </a>
            </div>
        </div>
    </div>
</template>

<script>
    import ArticleSkeleton from './../skeleton/ArticleSkeleton.vue';
    export default {
        props: ['type', 'group'],
        data() {
            return {
                articles: [],
                isLoadingArticle: true
            }
        },
        components: {
            ArticleSkeleton
        },
        created() {
            if (this.type == 'group')
                var url = '/api/articles/?group=' + this.group;
            else
                var url = '/api/articles';
            axios.get(url)
                 .then((response) => {
                    this.articles = response.data.data
                    this.isLoadingArticle = false
                 })
        },
        methods: {
            str_limit(str) {
                if(str.length > 75)
                    return str.substring(0,65) + '...';
                return str;
            },
        },
    }
</script>