@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css" />
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
<main class="main" role="main">
    <div class="pb-5 pt-3 bg-lightest-brand">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div class="mb-2">
                        <a href="/home"><i class="icon-chevron-small-left"></i> @lang('messages.Dashboard')</a>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h2>{{$pageSetting}}</h2>
                        </div>
                         
                        <div class="card-body">
                        <div class="col-md-4" style="float: right;padding-bottom: 15px;">
                        <input id="myInput" type="text" class="form-control" placeholder="Search ..">
                        </div>
                            <table class="table mt-2 table-bordered ">
                                <thead>
                                    <tr>
                                        <th scope="col"><b>Id</b></th>
                                        <th scope="col"><b>Title</b></th>
                                        <th scope="col"><b>Slug</b></th>
                                        <th scope="col"><b>Created date</b></th>
                                        <th scope="col"><b>Created by</b></th>
                                        <th scope="col" style="text-align: right;"><b>Action</b></th>
                                    </tr>
                                </thead>
                                <tbody id="myTable">
                                    @php $i= 1; @endphp
                                    @foreach ($pages as $page)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td class="cursor-pointer" onclick="showpop(event,'/pages/{{$page->id}}/{{$page->slug}}',this)">{{ $page->title }}</td>
                                        <td class="cursor-pointer" onclick="showpop(event,'/pages/{{$page->id}}/{{$page->slug}}',this)">{{ $page->slug }}</td>
                                        <td>{{ $page->created_at }}</td>
                                        <td>{{$page->user->name}}</td>
                                        <td style="text-align: right;">
                                            <a onclick="showpop(event,'/pages/{{$page->id}}/{{$page->slug}}',this)" class="cursor-pointer">
                                                <div class="popup-preview cursor-pointer"></div>View
                                            </a>

                                        </td>

                                    </tr>
                                
                                @endforeach
                                </tr>
                                </tbody>
                            </table>
                            <div class="card-footer">
                                <div class="d-flex justify-content-center">
                                    {{ $pages->links() }}
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->
    <div class="modal fade px-0" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <button type="button" class="close position-absolute zindex-dropdown right-0 text-light" data-dismiss="modal" aria-label="Close" id="cross-icon">
            <span aria-hidden="true">&times;</span>
        </button>
        <div class="modal-dialog modal-dialog-centered modal-xl m-auto" role="document">

            <div class="modal-content modal-content-pages bg-transparent border-0">
                <div class="modal-body text-center p-0" id="pop-up">
                    <iframe src="" frameborder="0"></iframe>
                </div>

            </div>
        </div>
    </div>
</main>



@endsection

@section('scripts')
<script>
    function showpop(event, obj_url, obj) {

        var ext = obj_url.split('.').pop().toUpperCase();

        var element = '';

        element = '<iframe src="' + obj_url + '"   style="background: #fff;height: 75vh!important;width: 75vw !important;"></iframe>';
        $('#pop-up').html(element);
        $('#exampleModal').modal('show');
        $('.popup-preview').css('background-size', '100% 100%');

    }
    $(document).ready(function() {
        $('#dtBasicExample').DataTable();
        $('.dataTables_length').addClass('bs-select');
    });
    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection