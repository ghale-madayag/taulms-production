<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\StudentGradeScore;
use App\Models\StudentQuiz;
use App\Models\StudentQuizStatus;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function start(Quiz $quiz){
  
        $quizzes = new StudentQuiz();
        $quizzes->user_id = request()->user()->id;
        $quizzes->quiz_id = $quiz->id;
        $quizzes->save();

        $lesson = $quiz->lesson()->first();
        $question = $quiz->question()->inRandomOrder()->first();

        return redirect('/quiz/'.$quiz->slug.'/question/'.$question->id);

    }

    public function active(Question $question){
        $user = request()->user()->id;
        $student = new StudentQuizStatus();

        $quiz = $student->where('quiz_id','=',$question->quiz_id)
            ->where('question_id','=',$question->id)
            ->where('user_id','=',$user)->get();

        if($quiz->count() == 0){
            $student->question_id = $question->id;
            $student->quiz_id = $question->quiz_id;
            $student->user_id = $user;
            $student->active = 1;
            $student->finished = 0;
            $student->save();
        }
    }

    public function show(Quiz $quiz, Question $question){
        $user = request()->user()->id;

        $query = $question->with('quiz')->find($question->id);
        $activeAnswer = StudentQuiz::where('finished',0)->where('user_id', $user)->where('quiz_id',$question->quiz_id)->orderBy('id')->first();

        //dd($activeAnswer);

        if($activeAnswer == null){
            $activeAnswer = StudentQuiz::where('finished',1)->where('user_id', $user)->where('quiz_id',$question->quiz_id)->orderBy('id')->first();
        }

        $query['data'] = json_decode($query['data']);

        if($activeAnswer->remaining != '00:00:00.0000000'){
            $time = $activeAnswer->remaining;
        }else{
            $time = $query['quiz']['end_date'];
        }


        $parsed = date_parse($time);
        $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

        $query['quiz']['duration'] = $seconds;

        return Inertia::render('Question/Index',[
            'question' => $query,
            'links' => $quiz->question()->select('id','quiz_id')->get(),
            'active' => $question->id,
            'finished_quiz' => $quiz->student_quiz_status,
            'activeAnswer' => Answer::where('question_id',$question->id)->where('student_quiz_id', $activeAnswer->id)->first()
        ]);
    }

    public function prev(Question $question){
        $user = request()->user()->id;

        StudentQuiz::where('user_id',$user)
            ->where('quiz_id',$question->quiz_id)
            ->update([
                'remaining' => request()->hrs.':'.request()->min.':'.request()->sec,
            ]);

    }

    public function finished(Question $question){

        request()->validate([
            'answers' => 'required',
        ]);

        $user = request()->user()->id;
        $query = $question->quiz()->find($question->quiz_id);
        $studentQuiz = $query->student_quiz()->where('quiz_id',$query->id)->where('user_id',$user)->first();
        $lesson = $query->lesson()->find($query->lesson_id);
        $subject = $lesson->subject()->find($lesson->subject_id);

        foreach (request()->answers as $questionId => $answers) {
            
            $points = 0;
            $checkAnswer = $question->answer;

            if(request()->hasFile('answers')){
                foreach(request()->answers as $file){
                    $file_name = time().''.rand(10,100).'-'. $file->getClientOriginalName();
                    $file->storeAs('/attachement/quiz', $file_name);
                    $data['answer'] = $file_name;
                    $answers = $file_name;
                }
                $points = -1;
            }else if(is_array($answers)){
                $checkAnswerArray = json_decode($checkAnswer, true);

                foreach ($checkAnswerArray as $answer) {
                    if (in_array($answer, $answers, true)) {
                        $points = $question->points;
                        break; // If a match is found, exit the loop
                    }
                }

            }else{
                if(!strcasecmp($checkAnswer, $answers)){
                    $points = $question->points;
                }
            }

            $checkQuestion = Answer::where('student_quiz_id',$studentQuiz->id)
                ->where('question_id', $questionId)
                ->get();

            if($checkQuestion->count() == 0){

                $answer = [
                    'student_quiz_id' => $studentQuiz->id,
                    'question_id' => $questionId,
                    'points' => $points,
                    'answer' => is_array($answers) ? json_encode($answers) : $answers
                ];

                Answer::create($answer);

            }else{

                if ($answers !== null) { // Check if $answers is not null
                    Answer::where('student_quiz_id', $studentQuiz->id)
                        ->where('question_id', $questionId)
                        ->update([
                            'points' => $points,
                            'answer' => is_array($answers) ? json_encode($answers) : $answers
                        ]);
                }else{
                    return redirect()->back()->with('error','test');
                }

            }
        }

        $student = new StudentQuizStatus();

        StudentQuiz::where('user_id',$user)
            ->where('quiz_id',$question->quiz_id)
            ->update([
                'remaining' => request()->hrs.':'.request()->min.':'.request()->sec,
            ]);


        $quiz = $student->where('quiz_id','=',$question->quiz_id)
            ->where('question_id','=',$question->id)
            ->where('finished',0)
            ->where('user_id','=',$user);

       if($quiz->count() != 0){
           $quiz->update(['finished' => 1]);
       }

       if(request()->complete){
       
           $checkTotalScore = Question::select(DB::raw('SUM(points) as total'))
               ->where('quiz_id', $question->quiz_id)
               ->first();

            $checkType = Question::
                whereIn('type', ['essay', 'file upload'])
                ->exists();

           $checkQuiz = Answer::select(DB::raw('SUM(points) AS score'))
               ->where('student_quiz_id',$studentQuiz->id)
               ->where('points', '!=', -1)
               ->groupBy('student_quiz_id')
               ->first();

            $term = getSelectedTerm();

            $attribute = [
                'user_id' => $user,
                'student_grade_sub_id'=> $query->student_grade_sub_id,
                'score'=> $checkQuiz->score,
                'term_id' => $term->id,
            ];

            StudentGradeScore::create($attribute);

            StudentQuiz::where('user_id',$user)
                ->where('quiz_id',$question->quiz_id)
                ->update([
                    'score' => $checkQuiz->score,
                    'total' =>  $checkTotalScore->total,
                    'finished' => 1,
            ]);

            if($checkType){
                return redirect()->back()->with(
                    'scoreAndTotal', array(
                        'score' => $checkQuiz->score,
                        'total' => $checkTotalScore->total,
                        'subject' => $subject->slug,
                        'lesson' => $lesson->slug,
                        'term' => $lesson->term,
                        'section' => $lesson->section_id,
                        'checkType' => $checkType
                    ),
                );
            }else{
                return redirect()->back()->with(
                    'scoreAndTotal', array(
                        'score' => $checkQuiz->score,
                        'total' => $checkTotalScore->total,
                        'subject' => $subject->slug,
                        'lesson' => $lesson->slug,
                        'term' => $lesson->term,
                        'section' => $lesson->section_id,
                        'checkType' => $checkType
                    ),
                );
            }


       }

    }

    public function savescore($question){

        $score = request()->score;
        $id = request()->id;
        $student = request()->student;

        $answer = Answer::find($id);
        

        if ($answer) {
            $answer->points = $score;
            $answer->save();


            $checkQuiz = Answer::select(DB::raw('SUM(points) AS score'))
               ->where('student_quiz_id', $answer->student_quiz_id)
               ->where('id', '!=', $answer->id)
               ->groupBy('student_quiz_id')
               ->first();
             
            if ($checkQuiz) {
                $totalScore = $checkQuiz->score + $score;

                //dd($answer->student_quiz_id);
                
                $studentGradeScore = StudentGradeScore::where('user_id', $student)
                ->where('id',$answer->student_quiz_id)
                ->first();

                $studentQuiz = StudentQuiz::where('user_id', $student)
                ->where('id',$answer->student_quiz_id)
                ->first();

                if ($studentGradeScore) {
                    $studentGradeScore->score = $totalScore;
                    $studentGradeScore->save();

                    $studentQuiz->score = $totalScore;
                    $studentQuiz->save();
                }

            }
        } 
    }




}
