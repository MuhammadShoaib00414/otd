<template>
	<div>
		<router-link :to="'/groups/'+group.slug">{{ group.name }}</router-link>
		<div v-for="subgroup in group.dashboard_subgroups_recursive">
			<group-recursive :group="subgroup"></group-recursive>
		</div>
	</div>
</template>

<script>
	import GroupRecursive from './GroupRecursive'

	export default {
		props: ['group'],
		data() {
			return {};
		},
		created() {
		},
		components: {
			GroupRecursive,
		}
	}
</script>