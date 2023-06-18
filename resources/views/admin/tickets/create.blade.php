@extends('admin.layout')

@section('page-content')
  <h5>New Ticket</h5>

  @foreach($errors->all() as $message)
    <div class="alert alert-dismissible alert-danger" style="max-width:450px;">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{!! $message !!}</strong>
    </div>
  @endforeach

  <form method="post" action="/admin/tickets" id="ticket">
      @csrf
      <div class="form-group mb-2">
          <label for="name">Ticket name</label>
          <input type="text" name="name" id="name" class="form-control" style="max-width: 450px;" required>
      </div>
      <div class="form-group mb-2">
        <label for="price">Base price</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text">$</span>
          </div>
          <input type="text" name="price" id="price" class="form-control" style="max-width: 414px;" required>
        </div>
      </div>


      <div class="mb-3">
          <p><b>Add-ons</b></p>
          <table>
              <tr class="my-2" v-for="(answer, index) in answerOptions">
                  <td>
                    <div class="col">
                      <span>Name</span>
                      <input type="text" :name="'addons['+index+'][name]'" class="form-control form-control-sm" v-model="answer.name">
                    </div>
                  </td>
                  <td>
                    <span>Price</span>
                    <input type="text" :name="'addons['+index+'][price]'" class="form-control form-control-sm" v-model="answer.price">
                  </td>
                  <td>
                      <a href="#" class="btn btn-sm btn-outline-primary" @click.prevent="answerOptions.splice(index,1)">&times;</a>
                  </td>
              </tr>
          </table>
          <a href="#" @click.prevent="answerOptions.push({value: ''})" class="btn btn-sm btn-outline-primary mt-2">Add</a>
      </div>

      <div class="mb-3">
          <p><b>Coupon Codes</b></p>
          <table>
              <tr class="my-2" v-for="(answer, index) in codeOptions">
                  <td>
                    <span>Code</span>
                    <input type="text" name="coupons[][code]" class="form-control form-control-sm" v-model="answer.code">
                  </td>
                  <td>
                    <span>Type</span>
                    <select name="coupons[][type]" class="custom-select custom-select-sm">
                      <option value="percent">Percent</option>
                      <option value="fixed">Fixed</option>
                    </select>
                  </td>
                  <td>
                    <span>Amount</span>
                    <input type="text" name="coupons[][amount]" class="form-control form-control-sm" v-model="answer.amount">
                  </td>
                  <td>
                      <a href="#" class="btn btn-sm btn-outline-primary" @click.prevent="codeOptions.splice(index,1)">&times;</a>
                  </td>
              </tr>
          </table>
          <a href="#" @click.prevent="codeOptions.push({value: ''})" class="btn btn-sm btn-outline-primary mt-2">Add</a>
      </div>

      <div class="form-group mt-3">
        <label for="registration_page">Assign to Registration Page</label><br>
        <select name="registration_page" id="registration_page" class="custom-select" style="max-width: 450px;">
          <option value="">None</option>
          @foreach($registration_pages as $page)
            <option value="{{ $page->id }}">{{ $page->name }}</option>
          @endforeach
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Create ticket</button>
  </form>
@endsection

@section('scripts')
@if(config('app.env') == 'development')
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js"></script>
@else
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.12"></script>
@endif
<script>
    var vm = new Vue({
        el: '#ticket',
        data: {
            type: 'Text',
            visibility: 'always',
            answerOptions: [],
            codeOptions: [],
        },
    })
</script>
@endsection