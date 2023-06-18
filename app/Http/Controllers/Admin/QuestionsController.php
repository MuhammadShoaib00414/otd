<?php

namespace App\Http\Controllers\Admin;

use App\Question;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function index()
    {
       	return view('admin.questions.index')->with([
    		'questions' => Question::whereNull('parent_question_id')->orderBy('id', 'asc')->get(),
    	]);
    }

    public function create(Request $request)
    {
        $parent = null;
        if ($request->has('parent'))
            $parent = Question::find($request->parent);

    	return view('admin.questions.create')->with([
            'parent' => $parent,
        ]);
    }

    public function store(Request $request)
    {
    	$question = Question::create([
            'prompt' => $request->prompt,
            'type' => $request->type,
            'options' => $request->options,
            'is_enabled' => $request->has('is_enabled'),
            'created_by' => $request->user()->id,
            'parent_question_id' => $request->parent_id,
            'visible_when_parent_is' => $request->visible_when_parent_is,
            'localization' => $request->localization,
            'locale' => $request->has('locale') ? $request->locale : 'en',
        ]);

    	return redirect('/admin/questions/'.$question->id);
    }

    public function update(Question $question, Request $request)
    {
    	$question->update([
            'prompt' => $request->prompt,
            'type' => $request->type,
            'options' => $request->options,
            'is_enabled' => $request->has('is_enabled'),
            'created_by' => $request->user()->id,
            'visible_when_parent_is' => $request->visible_when_parent_is,
            'locale' => $request->has('locale') ? $request->locale : 'en',
        ]);

    	return redirect('/admin/questions/'.$question->id);
    }

    public function edit(Question $question)
    {
    	return view('admin.questions.edit')->with([
    		'question' => $question,
    	]);
    }

    public function destroy(Question $question)
    {
    	$question->delete();

    	return redirect('/admin/questions');
    }

    public function show(Question $question)
    {
    	return view('admin.questions.show')->with([
    		'question' => $question,
    	]);
    }

    public function indexSort()
    {
        return view('admin.questions.sort')->with([
            'questions' => Question::whereNull('parent_question_id')->orderBy('order_key')->get(),
            'count' => 1,
        ]);
    }

    public function sort(Request $request)
    {
        foreach($request->questions as $question => $order_key)
        {
            Question::where('id', $question)->update(['order_key' => $order_key]);
        }

        return response(200);
    }
}
