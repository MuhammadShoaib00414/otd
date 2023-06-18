<template>
	<div>
		<div style="position:fixed; bottom: 0px; background-color: white; width: 100vw; z-index: 10000000;" class="w-100 d-flex justify-content-between py-1 d-md-none">
	        <a :tag="(index == 0) ? 'router-link' : 'a'" v-for="(link, index) in links" :href="link.url" class="col text-center">
	        	<img class="mx-auto" :src="link.icon_url" style="max-width: 30px;">
	        </a>
		</div>
	</div>
</template>

<script>
export default {
	name: 'MobileLinks',
	props: [],
	data() {
		return {
			links: [],
		};
	},
	created() {
		axios.get('/api/mobile-links')
			.then((response) => {
				this.links = response.data;
			});
	},
}
</script>
