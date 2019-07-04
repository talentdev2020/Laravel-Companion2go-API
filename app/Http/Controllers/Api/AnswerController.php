<?php

namespace App\Http\Controllers\Api;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{
    public function index()
    {
        return ['answers' => Answer::with(['question'])
            ->where('user_id', Auth::user()->id)
            ->get()
        ];
    }

    public function store(Requests\AnswerCreateRequest $request)
    {
        if(Answer::where('user_id', Auth::user()->id)
            ->where(['question_id' => $request->question_id, 'answer_type' => $request->answer_type])->first()
        ) {
            return new Response([
                'error' => 'This question is already answered',
            ], 409);
        }
        $answer = new Answer($request->all());
        $answer->user_id = Auth::user()->id;
        if (!policy($answer)->isCreatableBy($answer, Auth::user())) {
            return new Response([
                'error' => 'You have not permission to answer this question',
            ], 403);
        }
        if (!policy($answer)->isAllowedFor($answer, Question::find($answer->question->id), Auth::user())) {

        }
        $answer->save();
        return new Response(['answer' => $answer,]);
    }

    public function show($id)
    {
        if (!$answer = Answer::with(['question','user',])->find($id)) {
            return new Response([
                'error' => 'Answer not found',
            ], 404);
        }
        if (!policy($answer)->isViewableBy($answer, Auth::user())) {
            return new Response([
                'error' => 'You have not permission to see this answer',
            ], 403);
        }
        return new Response(['answer' => $answer]);
    }
}
