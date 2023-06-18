<template>
<div>
    <div class="card mb-2">
        <div class="card-body d-flex justify-content-between pb-2" style="border-bottom: 1px solid rgb(233, 236, 239);">
            <div>
                <div class="row">
                    <vue-skeleton-loader
                        type="circle"
                        :width="50"
                        :height="50"
                        animation="fade"
                        class="mr-2"
                        :color="'#f7f7f7'"
                        :wave-color="siteColor"
                    />
                    <div>
                        <span class="d-block font-weight-bold" style="line-height: 1;">
                            <vue-skeleton-loader
                                type="rect"
                                :width="140"
                                :height="17"
                                animation="fade"
                                class="mb-2 rounded"
                                :color="'#f7f7f7'"
                                :wave-color="siteColor"
                            />
                        </span>
                        <span style="line-height: 1;">
                            <vue-skeleton-loader
                                type="rect"
                                :width="160"
                                :height="17"
                                animation="fade"
                                class=" rounded"
                                :color="'#f7f7f7'"
                                :wave-color="siteColor"
                            />
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right" style="line-height: 1.3;">
                <vue-skeleton-loader
                    type="rect"
                    :width="100"
                    :height="17"
                    animation="fade"
                    class="mb-2 rounded"
                    :color="'#f7f7f7'"
                    :wave-color="siteColor"
                />
                <vue-skeleton-loader
                    type="rect"
                    :width="80"
                    :height="17"
                    animation="fade"
                    class="ml-auto rounded"
                    :color="'#f7f7f7'"
                    :wave-color="siteColor"
                />
            </div>
        </div>
        <div class="card-body">
            <div>
                <div class="pt-3 p-1 #eca7dc2eactor-output">
                    <p>
                        <vue-skeleton-loader v-for="i in 5" :key="i"
                            type="rect"
                            :width="(i % 2 == 0) ? '100%' : '100%'"
                            :height="'15px'"
                            animation="fade"
                            class="mb-2 rounded"
                            :color="'#f7f7f7'"
                            :wave-color="siteColor"
                        />
                    </p>
                </div>
                
            </div>
        </div>
        <div class="card-footer text-muted py-0"
            style="position: relative; background-color: rgb(249, 250, 251); min-height: 40px;">
            <div class="d-flex align-items-center footer-content">
                <div class="text-center mt-1">
                    <vue-skeleton-loader
                        type="rect"
                        :width="50"
                        :height="25"
                        animation="fade"
                        class="m-auto rounded"
                        :color="'#e9ecef'"
                        :wave-color="siteColor"
                    />
                </div>
                <div class="text-center d-flex align-items-center ml-2 mt-1">
                    <vue-skeleton-loader
                        type="rect"
                        :width="50"
                        :height="25"
                        animation="fade"
                        class="mr-auto rounded"
                        :color="'#e9ecef'"
                        :wave-color="siteColor"
                    />
                </div>
                <div class="ml-auto text-center mt-1">
                    <vue-skeleton-loader
                        type="rect"
                        :width="200"
                        :height="14"
                        animation="fade"
                        class="ml-auto rounded"
                        :color="'#e9ecef'"
                        :wave-color="siteColor"
                    />
                    
                </div>
                
            </div>
            
        </div>
    </div>
    
    
    
</div>
</template>

<script>
import VueSkeletonLoader from 'skeleton-loader-vue';

export default {
    data() {
        return {
            siteColor: 'white',
        }
    },
    mounted() {
        this.siteColor = window.getComputedStyle(document.body).getPropertyValue('--site-color');
    },
    components: { VueSkeletonLoader },
}
</script>
