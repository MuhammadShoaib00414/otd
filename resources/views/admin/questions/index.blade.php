@extends('admin.layout')

@section('page-content')

    @component('admin.partials.breadcrumbs', ['links' => [
        'Profile Questions' => '/admin/questions',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between">
        <div class="d-flex align-items-center justify-content-start">
            <h5 class="mr-4 mb-0">Custom Profile Questions</h5>
        </div>
        <div class="text-right">
            <a class="btn btn-outline-primary btn-sm" href="/admin/questions/sort">
                Sort Questions
            </a>
            <a class="btn btn-primary btn-sm" href="/admin/questions/create">
              Add Question
            </a>
        </div>
    </div>
    
    <table class="table mb-0 mt-2">
                <thead>
                    <tr>
                        <td><b>prompt</b></td>
                        <td><b>status</b></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question )
                        <tr>
                            <td>
                                {{ $question->prompt }}<br>
                                <span class="text-small text-muted">{{ $question->type }}</span>
                            </td>
                            <td style="vertical-align: middle;">
                                @if($question->is_enabled)
                                    <span class="badge badge-primary">enabled</span>
                                @else
                                    <span class="badge badge-secondary">disabled</span>
                                @endif
                            </td>
                            <td class="text-right" style="vertical-align: middle;">
                                <a href="/admin/questions/{{ $question->id }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
@endsection