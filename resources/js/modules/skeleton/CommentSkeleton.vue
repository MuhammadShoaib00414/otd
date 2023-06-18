<template>
    <div class="ml-1 mb-2 d-flex align-items-center">
        <vue-skeleton-loader
            type="circle"
            :width="60"
            :height="60"
            animation="fade"
            class="mr-2"
            :color="'#e9ecef'"
            :wave-color="'red'"
        />
        <div class="ml-1 mt-1">
            <vue-skeleton-loader
                type="rect"
                :width="'100%'"
                :height="'15px'"
                animation="fade"
                class="mb-2 rounded"
                :color="'#e9ecef'"
                :wave-color="'red'"
            />
            <vue-skeleton-loader
                type="rect"
                :width="'100%'"
                :height="'15px'"
                animation="fade"
                class="mb-2 rounded"
                :color="'#e9ecef'"
                :wave-color="'red'"
            />
            <vue-skeleton-loader
                type="rect"
                :width="180"
                :height="15"
                animation="fade"
                class="mb-2 rounded"
                :color="'#e9ecef'"
                :wave-color="'red'"
            />
        </div>
        <div class="ml-auto">
            <vue-skeleton-loader
                type="circle"
                :width="30"
                :height="30"
                animation="fade"
                class="m-auto mb-2"
                :color="'#e9ecef'"
                :wave-color="'red'"
            />
            <vue-skeleton-loader
                type="rect"
                :width="40"
                :height="12"
                animation="fade"
                class="ml-auto rounded"
                :color="'#e9ecef'"
                :wave-color="'red'"
            />

        </div>
    </div>
</template>

<script>
    import VueSkeletonLoader from 'skeleton-loader-vue';

    export default {
        name: 'GroupsListSkeleton',
        components: {
            VueSkeletonLoader
        }
    }
</script>