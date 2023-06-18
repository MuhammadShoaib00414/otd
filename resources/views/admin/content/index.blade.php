@extends('admin.content.layout')

@section('head')
@parent
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
@endsection

@section('inner-page-content')
<div class="d-flex justify-content-between">
	<h5>Live on site</h5>
	<form method="get" style="white-space: nowrap;">
		<label for="sort">Sort by:</label>
		<select class="custom-select custom-select-sm" name="sort" id="sort">
			<option value="dateDesc" {{ request()->has('sort') && request()->sort == "dateDesc" ? 'selected' : '' }}>Date posted: Newest to Oldest</option>
			<option value="dateAsc" {{ request()->has('sort') && request()->sort == "dateAsc" ? 'selected' : '' }}>Date posted: Oldest to Newest</option>
			<option value="titleAsc" {{ request()->has('sort') && request()->sort == "titleAsc" ? 'selected' : '' }}>Title: A-Z</option>
			<option value="titleDesc" {{ request()->has('sort') && request()->sort == "titleDesc" ? 'selected' : '' }}>Title: Z-A</option>
			<option value="clicksDesc" {{ request()->has('sort') && request()->sort == "clicksDesc" ? 'selected' : '' }}>Clicks: Highest to Lowest</option>
			<option value="clicksAsc" {{ request()->has('sort') && request()->sort == "clicksAsc" ? 'selected' : '' }}>Clicks: Lowest to Highest</option>
		</select>
	</form>
	<div class="text-right">
		<button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#export">
			<i class="fas fa-download"></i> Export
		</button>
		<a class="btn btn-primary btn-sm" href="/admin/content/articles/add">
			Add content
		</a>
	</div>
</div>

<table class="table mt-2">
	<thead>
		<tr>
			<th scope="col"></th>
			<th scope="col"><b>Title</b></th>
			<th scope="col"><b>Group</b></th>
			<th scope="col"><b>Clicks</b></th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tbody>
		@foreach($articles as $article)
		@if($article->listing)
			<tr>
				<td>
					<a href="{{ $article->url }}" target="_blank" style="display: block; height: 3em; width: 3em; background-image: url('{{ $article->image_url }}'); background-size: cover; background-position: center;"></a>
				</td>
				<td>
					<a href="{{ $article->url }}" target="_blank">{{ $article->title }}</a><br>
					{{ $article->created_at->toFormattedDateString() }}
				</td>
				<td>
					@foreach($article->listing->groups as $group)
					<a href="/admin/groups/{{ $group->id }}/">{{ $group->name }}</a>@if(!$loop->last), @endif 
					@endforeach
				</td>
				<td>
					{{ $article->clicks }}
				</td>
				<td class="text-right">
					<a href="/admin/content/articles/{{ $article->id }}/edit">Edit</a>
					<a href="/admin/content/articles/{{ $article->id }}">View</a>
				</td>
			</tr>
		@endif
		@endforeach
	</tbody>
</table>

<div class="d-flex justify-content-center">
	{{ $articles->links() }}
</div>

<div class="modal fade" id="export" tabindex="-1" role="dialog" aria-labelledby="export" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Export Content</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="get" action="/admin/content/articles/export">
				<div class="modal-body">
					<div class="form-group">
						<label for="start_date">From <small class="text-muted">(optional)</small></label>
						<input type="text" autocomplete="off" class="form-control" name="start_date" id="start_date" placeholder="mm/dd/yy">
					</div>
					<div class="form-group">
						<label for="end_date">To <small class="text-muted">(optional)</small></label>
						<input type="text" autocomplete="off" class="form-control" name="end_date" id="end_date" placeholder="mm/dd/yy">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Export to CSV</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
	@if(request()->has('sort'))
	$('.pagination li a').each(function() {
		$(this).attr('href', $(this).attr('href') + '&sort={{ request()->sort }}');
	});

	@endif

	$('#sort').change(function(e) {
		this.form.submit();
	});
	$('#start_date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });

    $('#end_date').datepicker({
      uiLibrary: 'bootstrap4',
      format: 'mm/dd/yy'
    });
</script>
@endsection