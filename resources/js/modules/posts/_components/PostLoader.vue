<template>
    <div>
        <!---->
        <div class="card mb-2 order-1">
            <div class="card-body d-flex justify-content-between pb-2"
                style="border-bottom: 1px solid rgb(233, 236, 239);">
                
                <div>
                    <div class="row"><a href="/users/377" class="d-flex no-underline font-dark">
                        <vue-skeleton-loader
                            type="circle"
                            :width="40"
                            :height="40"
                            animation="fade"
                        />
                        <div class="ml-1">
                            <span class="d-block font-weight-bold" style="line-height: 1;">
                                <vue-skeleton-loader
                                    type="rect"
                                    :width="100"
                                    :height="15"
                                    animation="wave"
                                    class="mb-1"
                                />
                            </span>
                            <span style="line-height: 1;">
                                <vue-skeleton-loader
                                    type="rect"
                                    :width="120"
                                    :height="15"
                                    animation="wave"
                                />
                            </span></div>
                        </a></div>
                </div>
                <div class="text-right" style="line-height: 1.3;">
                    <vue-skeleton-loader
                        type="rect"
                        :width="60"
                        :height="15"
                        animation="wave"
                        class="mb-1"
                    />
                    <vue-skeleton-loader
                        type="rect"
                        :width="70"
                        :height="15"
                        animation="wave"
                    />
                </div>
            </div>
            <div class="card-body">
                <div>
                    <div class="pt-3 p-1 redactor-output">
                        <p>
                            <vue-skeleton-loader
                                type="rect"
                                :width='"100%"'
                                :height='"15px"'
                                animation="wave"
                                class="mb-1"
                            />
                            <vue-skeleton-loader
                                type="rect"
                                :width='"100%"'
                                :height='"15px"'
                                animation="wave"
                                class="mb-1"
                            />
                            <vue-skeleton-loader
                                type="rect"
                                :width='"100%"'
                                :height='"15px"'
                                animation="wave"
                                class="mb-1"
                            />
                            <vue-skeleton-loader
                                type="rect"
                                :width='"100%"'
                                :height='"15px"'
                                animation="wave"
                                class="mb-1"
                            />
                            <vue-skeleton-loader
                                type="rect"
                                :width='"100%"'
                                :height='"15px"'
                                animation="wave"
                                class="mb-1"
                            />
                            <vue-skeleton-loader
                                type="rect"
                                :width='"100%"'
                                :height='"15px"'
                                animation="wave"
                                class="mb-1"
                            />
                        </p>
                    </div>
                    <!---->
                </div>
            </div>
            <div class="card-footer text-muted py-0"
                style="position: relative; background-color: rgb(249, 250, 251); min-height: 40px;">
                <div class="d-flex align-items-center footer-content">
                    <div class="text-center">
                        <div class="d-flex align-items-center"><i class="likeButton icon-heart"></i>
                            <div style="font-size: 0.8em; cursor: pointer; margin-left: 6px; font-weight: 500;">1</div>
                        </div>
                    </div>
                    <div class="text-center d-flex align-items-center ml-1"><i
                            class="likeButton text-dark icon-message"></i> <span
                            style="font-size: 0.8em; cursor: pointer; margin-left: 6px; font-weight: 500;">15</span>
                    </div>
                    <div class="ml-auto text-center">
                        <div><a href="/groups/sun-protection-for-the-summer" class="mx-auto"><small>Sun Protection for
                                    the Summer</small></a></div>
                        <!---->
                    </div>
                    <!---->
                </div>
                <!---->
            </div>
        </div>
        <!---->
        <!---->
        <!---->
    </div>
</template>

<script>
    import VueSkeletonLoader from 'skeleton-loader-vue';
    export default {
        data() {
            return {}
        },
        components: {
            VueSkeletonLoader
        }
    }
</script>