<template>
    <div class="bg-light-brand p-3 mb-2">
        <vue-skeleton-loader
            type="rect"
            :width="150"
            :height="15"
            animation="fade"
            class="mb-2 rounded"
            :color="'#f7f7f7'"
            :wave-color="'red'"
        />
        <vue-skeleton-loader
            type="rect"
            :width="80"
            :height="15"
            animation="fade"
            class="mb-2 rounded"
            :color="'#f7f7f7'"
            :wave-color="'red'"
        />
        <vue-skeleton-loader
            type="rect"
            :width="200"
            :height="40"
            animation="fade"
            class="mb-2 rounded"
            :color="'#f7f7f7'"
            :wave-color="'red'"
        />
    </div>
</template>

<script>
import VueSkeletonLoader from 'skeleton-loader-vue';

export default {
    components: {
        VueSkeletonLoader
    }
}
</script>