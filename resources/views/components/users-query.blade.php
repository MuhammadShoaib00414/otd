@push('stylestack')
    <link rel="stylesheet" href="https://unpkg.com/vue-query-builder@0.8.2/dist/VueQueryBuilder.css" />
@endpush

<div id="queryApp">
    <vue-query-builder :rules="rules" v-model="query"></vue-query-builder>
    <input type="hidden" name="query_builder_users" v-bind:value="JSON.stringify(users)">
    <input type="hidden" name="query_builder_query" v-bind:value="JSON.stringify(query)">
    <p class="mt-3">@{{ users.length }} @lang('messages.users')</p>
</div>

@push('scriptstack')
    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
    <script src="https://unpkg.com/vue-query-builder@0.8.2/dist/VueQueryBuilder.umd.min.js"></script>
    <script>
        new Vue({
            el: '#queryApp',
            data: {
                rules: JSON.parse(`{!! $rules->toJson() !!}`),
                query: JSON.parse(`{!! $query !!}`),
                users: [],
            },
            components: {
                VueQueryBuilder: window.VueQueryBuilder
            },
            watch: {
                query: function (val) {
                    vthis = this;
                    $.ajax({
                        'method': 'POST',
                        'url': '/api/users/query-builder',
                        'data': {
                            '_token': "{{ csrf_token() }}",
                            'query': JSON.stringify(val),
                        },
                        success: function (response) {
                            vthis.users = response;
                        },
                    })
                }
            }
        });
    </script>
@endpush