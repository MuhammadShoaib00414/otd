<template>
    <div v-if="post.is_reported" style="background-color: #d03232; padding: 0.1em 0.5em; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; position: absolute; top: 0; left: 50%; transform: translateX(-50%); color: #fff;">
        {{ $__.posts['Reported'] }}
    </div>
</template>

<script>
export default {
    props: ['post'],
    data() {
        return {};
    },
}
</script>