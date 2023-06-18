@section('scripts')
<!-- include libraries(jQuery, bootstrap) -->
<link href="/editor/res/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

@push('scriptstack')
@if(Session::has('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script>
  Swal.fire({
    title: 'Success!',
    text: 'Successfully updated.',
    type: 'success',
    confirmButtonText: 'Close'
  })
</script>
@endif
@php
Session::forget('success');
@endphp
@endpush
<script>


  jQuery(function() {
    $("#urlTitle").css('display', 'none');
    $("#title").blur(function() {
      var title = this.value;
      // ^[a-z-0-9][A-Za-z0-9 ]+$
      if (/[a-zA-Z]+/.test(title)) {
        $("#urlTitle").css('display', 'none');
        return (true)
      } else if (title.length == 0) {
        $("#urlTitle").css('display', 'none');

      } else {
        $("#urlTitle").css('display', 'block');
        return (false)
      }

    });

  });


  setInterval(function() {
    $('.rx-container').tooltip('dispose');
  }, 60 * 100);
  $('#copyLink').click(function(event) {
    event.preventDefault();
    var copy_text = $('#link').val();
    navigator.clipboard.writeText(copy_text);
    $(this).html('Copied!');
  });

  $('.content').summernote({
  height: 350,   //set editable area's height
  codemirror: { // codemirror options
    theme: 'monokai'
  }
});
 
</script>
@endsection