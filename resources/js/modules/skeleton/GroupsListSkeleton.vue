<template>
    <div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="200"
                :height="17"
                animation="fade"
                class="mb-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
            <div class="ml-2">
                <vue-skeleton-loader
                    type="rect"
                    :width="200"
                    :height="17"
                    animation="fade"
                    class="mb-2 rounded"
                    :color="'#efefef'"
                    :wave-color="'red'"
                />
            </div>
        </div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="250"
                :height="17"
                animation="fade"
                class="mb-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
            <div class="ml-2">
                <vue-skeleton-loader
                    type="rect"
                    :width="250"
                    :height="17"
                    animation="fade"
                    class="mb-2 rounded"
                    :color="'#efefef'"
                    :wave-color="'red'"
                />
            </div>
            <div class="ml-2">
                <vue-skeleton-loader
                    type="rect"
                    :width="250"
                    :height="17"
                    animation="fade"
                    class="mb-2 rounded"
                    :color="'#efefef'"
                    :wave-color="'red'"
                />
                <div class="ml-2">
                    <vue-skeleton-loader
                        type="rect"
                        :width="250"
                        :height="17"
                        animation="fade"
                        class="mb-2 rounded"
                        :color="'#efefef'"
                        :wave-color="'red'"
                    />  
                </div>
            </div>
        </div>
        <div>
            <vue-skeleton-loader
                type="rect"
                :width="200"
                :height="17"
                animation="fade"
                class="mb-2 rounded"
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
                class="mb-2 rounded"
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
                class="mb-2 rounded"
                :color="'#efefef'"
                :wave-color="'red'"
            />
            <div class="ml-2">
                <vue-skeleton-loader 
                    type="rect"
                    :width="200"
                    :height="17"
                    animation="fade"
                    class="mb-2 rounded"
                    :color="'#efefef'"
                    :wave-color="'red'"
                />
            </div>
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