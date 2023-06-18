@extends('admin.layout')

@section('head')
@parent
<style>
.sortable-item:hover {
    cursor: pointer;
}
.sortable-chosen {
    cursor: grabbing;
}
</style>
@endsection

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Profile Questions' => '/admin/questions',
        'Sort' => '',
    ]])
    @endcomponent

<div class="col-12 col-sm-10 col-md-10 col-lg-8 mx-auto">
  <div id="successMessage" class="d-none alert alert-dismissible alert-success">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Changes saved successfully!</strong>
  </div>
    <div class="d-flex justify-content-between py-2">
        <h5 class="mr-4 mb-0">Sort Questions</h5>
        <button id="submitButton" class="btn btn-sm btn-primary">@lang('general.save')</button>
    </div>
    <p>Define the order in which questions appear when a user is filling out their profile.</p>
    <div class="card">
        <div class="card-header text-center">
            <small class="text-muted"> Drag items to sort. </small>
        </div>
        <div id="0" data-accepts="0" class="contains sortable-item" style="margin: 0.5em;border: 1px solid #fff;">
            @foreach($questions as $question)
                @include('admin.questions.partials.sort', ['question' => $question, 'count' => $count, 'last' => $loop->last])
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/vue/2.5.2/vue.min.js"></script>
<!-- CDNJS :: Sortable (https://cdnjs.com/) -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
<script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
<script type="module">
var containers = null;
containers = document.querySelectorAll(".contains");
for (var i = 0; i < containers.length; i++) {
  new Sortable(containers[i], {
      group: {
        name: "sortable-list-2",
        pull: true,
        put: function(to, from, dragEl) {
            return to.el.dataset.accepts === dragEl.dataset.type;
        }
      },
      animation: 250,
      forceFallback: true,
    });
}

$('#submitButton').click(function() {
    var sorted = {};
    var containers = $('.contains');
    for(var i = 0; i < containers.length; i++)
    {
        var items = $(containers[i]).children();
        for(var j = 0; j < items.length; j++)
        {
            sorted['' + $(items[j]).prop('id')] = j + 1;
        }
    }
    $.ajax({
        url: "/admin/questions/sort", 
        type : "PUT",
        data : 
        {
            "questions": sorted,
            "_token": "{{ csrf_token() }}",
        },
        success: function () {
            $('#successMessage').removeClass('d-none');
        }
    });
});
</script>
<style scoped></style>
@endsection