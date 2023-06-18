@extends('admin.layout')

@section('page-content')
    @component('admin.partials.breadcrumbs', ['links' => [
        'Profile Questions' => '/admin/questions',
        $question->prompt => '',
    ]])
    @endcomponent

    <div class="d-flex justify-content-between align-items-center">
        <h5>{{ $question->prompt }}</h5>
        <a href="/admin/questions/{{ $question->id }}/edit" class="btn btn-sm btn-primary">Edit</a>
    </div>
    <p>Type: <span class="text-muted">{{ $question->type }}</span></p>
    <p>Parent: 
        @if($question->parent)
            <span class="text-muted"><a href="/admin/questions/{{ $question->parent->id }}">{{ $question->parent->prompt }}</a>
        @else
            <span class="text-muted">none</span>
        @endif
    </p>
    @if($question->type == 'Dropdown menu')
    <hr>
    <p><b>Answer options:</b> {{ collect($question->options)->implode(', ') }}</p>
    @endif
    <hr>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Sub-Questions</span>
            <a href="/admin/questions/create?parent={{ $question->id }}" class="btn btn-sm btn-primary">New</a>
        </div>
        @if($question->children->count() == 0)
            <div class="card-body">
                <p class="text-center text-muted my-5">No sub-questions</p>
            </div>
        @else
            <table class="table mb-0">
                <thead>
                    <tr>
                        <td><b>prompt</b></td>
                        <td><b>show if parent answer is</b></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($question->children as $childQuestion )
                        <tr>
                            <td>
                                {{ $childQuestion->prompt }}<br>
                                <span class="text-muted text-small">{{ $childQuestion->type }}</span>
                            </td>
                            <td style="vertical-align: middle;">
                                @if($childQuestion->visible_when_parent_is)
                                    {{ $childQuestion->visible_when_parent_is }}
                                @else
                                    <i class="text-muted">always visible</i>
                                @endif
                            </td>
                            <td style="vertical-align: middle;">
                                @if($childQuestion->is_enabled)
                                    <span class="badge badge-primary">enabled</span>
                                @else
                                    <span class="badge badge-secondary">disabled</span>
                                @endif
                            </td>
                            <td class="text-right" style="vertical-align: middle;">
                                <a href="/admin/questions/{{ $childQuestion->id }}/edit">Edit</a> - 
                                <a href="/admin/questions/{{ $childQuestion->id }}">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
@endsection