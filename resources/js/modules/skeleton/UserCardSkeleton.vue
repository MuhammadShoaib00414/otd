<template>
    <div class="card mb-2">
        <div class="card-body p-1">
            <div class="ml-1 d-flex align-items-center">
                <vue-skeleton-loader
                    type="circle"
                    :width="50"
                    :height="50"
                    animation="fade"
                    class="mr-2"
                    :color="'#f7f7f7'"
                    :wave-color="'red'"
                />
                <div class="ml-1 mt-1">
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
                        :width="180"
                        :height="15"
                        animation="fade"
                        class="mb-2 rounded"
                        :color="'#f7f7f7'"
                        :wave-color="'red'"
                    />
                </div>
            </div>
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