<template>
    <div class="mb-3 mt-1">
        <div
            target="_blank" class="no-underline"
            style="background-color: #ededed; height: 12em; display: flex; align-items: flex-end;animation: fade 1.5s linear .5s infinite;">
            
            <span
                class="d-block px-2 py-1 mb-2 border-primary"
                style="background-color: rgb(255, 255, 255); width: 85%; color: rgb(30, 30, 32); transform: translateX(-5px); border-left-width: 5px; border-left-style: solid;">

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
                <vue-skeleton-loader
                    type="rect"
                    :width="150"
                    :height="15"
                    animation="fade"
                    class="mb-2 rounded"
                    :color="'#f7f7f7'"
                    :wave-color="'red'"
                />
            </span>
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