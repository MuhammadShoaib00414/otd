<div v-if="type == 'Dropdown menu' || type == 'Multi-select'" class="mb-3">
    <p><b>Options</b></p>
    <table>
        <thead>
            <tr>
                <th><small>English</small></th>
                @if(getsetting('is_localization_enabled'))
                <th><small>Español</small></th>
                @endif
            </tr>   
        </thead>
        <tr v-for="(answer, index) in answerOptions">
            <td>
                <input type="text" name="options[]" class="form-control form-control-sm" v-model="answer.value" :required="type == 'Dropdown menu' || type == 'Multi-select' ? true : false">
            </td>
            @if(getsetting('is_localization_enabled'))
            <td>
                <input type="text" name="localization[es][options][]">
            </td>
            @endif
            <td>
                <a href="#" class="btn btn-sm btn-outline-primary" @click.prevent="answerOptions.splice(index,1)">&times;</a>
            </td>
        </tr>
    </table>
    <a href="#" @click.prevent="answerOptions.push({value: ''})" class="btn btn-sm btn-outline-primary">Add</a>
</div>