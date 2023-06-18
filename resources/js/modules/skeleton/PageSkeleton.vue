<template>
   <div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="100"
                :height="25"
                animation="fade"
                class="mb-2 ml-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
        </div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="200"
                :height="17"
                animation="fade"
                class="mb-2 ml-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
        </div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="200"
                :height="17"
                animation="fade"
                class="mb-2 ml-2 pl-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
        </div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="200"
                :height="17"
                animation="fade"
                class="mb-2 ml-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
        </div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="200"
                :height="17"
                animation="fade"
                class="mb-2 ml-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
        </div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="200"
                :height="17"
                animation="fade"
                class="mb-2 ml-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
        </div>
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