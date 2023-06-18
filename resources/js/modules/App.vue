<template>
    <div>
        <nav-bar v-if="haveResourcesLoaded"></nav-bar>
        <router-view :key="$route.fullPath"></router-view>
        <Footer></Footer>
        <MobileLinks></MobileLinks>
        <PagesModal></PagesModal>
    </div>
</template>
<script>
    
    import MobileLinks from './_components/MobileLinks'
    import NavBar from './_components/NavBar'
    import Footer from './_components/Footer'
    import PagesModal from './_components/PagesModal'

    export default {
        components: {
            MobileLinks,
            NavBar,
            Footer,
            PagesModal
        },
        methods: {
            haveResourcesLoaded() {
                return this.$user;
            }
        }
    }
</script>