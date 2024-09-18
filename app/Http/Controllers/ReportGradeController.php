<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;
use App\Models\StudentGradeCat;
use App\Models\StudentGradeMark;
use App\Models\StudentGradeScore;
use App\Models\StudentGradeSub;
use App\Models\Term;
use Illuminate\Validation\Rule;

class ReportGradeController extends Controller
{
    public function index(Subject $subject, Section $section, $term, $cat, $tabs){

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);
        
        $students = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',)
        ->whereHas('registration', function($query) use ($subject, $section, $term){
            $query->whereHas('registration_details', function($query) use ($subject, $section, $term){
                $query->whereHas('schedule', function($query) use ($subject, $section, $term){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id)->where('term_id', $term);
                });
            });
        })
        ->orderBy('users.lname', 'ASC')
        ->get(['user_id']);

        // $criteria = StudentGradeSub::
        // whereHas('student_grade_cat', function ($query) use ($user, $cat, $tabs, $subject, $section) {
        //         $query->where('user_id', $user->id)->where('subject_id',$subject->id)->where('section_id',$section->id)->where('tabs', $tabs)->where('category', $cat);
               
        //     })
        //     ->orderBy('student_grade_cat.position', 'ASC')
        //     ->get(['id', 'name', 'over', 'student_grade_cat_id']);

        $criteria = StudentGradeSub::join('student_grade_cats', 'student_grade_subs.student_grade_cat_id', '=', 'student_grade_cats.id')
            ->where('student_grade_cats.user_id', $user->id)
            ->where('student_grade_cats.subject_id', $subject->id)
            ->where('student_grade_cats.section_id', $section->id)
            ->where('student_grade_cats.tabs', $tabs)
            ->where('student_grade_cats.category', $cat)
            ->orderBy('student_grade_cats.position', 'ASC')
            ->get(['student_grade_subs.id', 'student_grade_subs.name', 'student_grade_subs.over', 'student_grade_subs.student_grade_cat_id']);

        $gradesData = [];

        foreach ($students as $student) {
            $studentGrades = [];
            $categoryWeights = [];

            foreach ($criteria as $ctr) {
                $scores = StudentGradeScore::where('user_id', $student->id)
                    ->whereHas('student_grade_sub', function ($query) use ($ctr) {
                        $query->where('student_grade_sub_id', $ctr->id);
                    })
                    ->select('id', 'score')
                    ->get();

                $totalScore = $scores->sum('score');
                $perfectScore = $ctr->over;
                $percentage = $ctr->student_grade_cat->percentage;

                $averageScore = ($perfectScore > 0) ? (($totalScore / $perfectScore) * 100) : 0;

                $formattedAverageScore = number_format($averageScore, 2);

                $studentGrades[$ctr->name] = [
                    'id' => $ctr->id,
                    'category_id' => $ctr->student_grade_cat_id,
                    'percentage' => $percentage,
                    'over' => $ctr->over,
                    'total_score' => $totalScore,
                    'average_score' => $formattedAverageScore,
                ];

              
                if (!isset($categoryWeights[$ctr->student_grade_cat_id])) {
                    $categoryWeights[$ctr->student_grade_cat_id] = 0;
                }
                $categoryWeights[$ctr->student_grade_cat_id] = $percentage;
            }

            // Calculate the weighted average for each category
            $categoryAverages = [];
            foreach ($categoryWeights as $categoryId => $weight) {
                $catAvg = 0;
                $categoryTotalScore = 0;
                $categoryTotalPercentage = 0;
                //dd($studentGrades);
                foreach ($studentGrades as $grade) {
                    //dd($categoryId);
                    if ((int)$grade['category_id'] === $categoryId) {
                        //dd('true');
                        $categoryTotalScore += ($grade['average_score'] * $grade['percentage']);
                        $categoryTotalPercentage += $grade['percentage'];
                    }
                }

                $categoryAverage = ($categoryTotalPercentage > 0) ? ($categoryTotalScore / $categoryTotalPercentage) : 0;
                $catAvg = $categoryAverage * ($weight / 100);
                $categoryAverages[$categoryId] = number_format($catAvg, 2);

            }


           
            // Fill missing quizzes with 0 scores
            foreach ($criteria as $ctr) {
                if (!isset($studentGrades[$ctr->name])) {
                    $studentGrades[$ctr->name] = 0;
                }
            }

            $totalSum = 0;

            foreach ($categoryAverages as $categoryId => $formattedValue) {
                $numericValue = (float) str_replace(',', '', $formattedValue);
                $totalSum += $numericValue;
            }

           // dd($totalSum);
            //calculate the over all percentage 
            //if $totalSum < 60%/Passing Rate 75-(25*(60-$totalSum)/60)
            //else 100-(25*(100-K13)/(100-G$2))

            $mark = StudentGradeMark::where('subject_id',$subject->id)
                    ->where('section_id', $section->id)
                    ->where('term_id', $term)
                    ->first();

            $passing = ($mark) ? $mark->passing : 60;
            $cutoff = ($mark) ? $mark->cutoff : 75;


            if($totalSum < $passing){
                $percentage = 75 - (25*($passing-$totalSum)/$passing);
            }else{
                $percentage = 100 - (25*(100-$totalSum)/(100-$passing));
            }

            //convert it to a readable grade
            //1+2*((100-L13)/(100-$cutoff))
            //75% is the cut-off

            $readableGrade = 1 + 2 *((100-$percentage)/(100-$cutoff));

            //rounded grade
            //=IF($readableGrade>3,MROUND($readableGrade,0.5),MROUND($readableGrade,0.25))
            $roundedGrade = 0;
            
            if ($readableGrade > 3) {
                $roundedGrade = round($readableGrade * 2) / 2;
            } else {
                $roundedGrade = round($readableGrade * 4) / 4;
            }

            
            if($tabs == 'finalgrade'){
                //lecture
                $midterm = collect($this->midtermAvg($subject,$section,$term))->first(function ($mid) use ($student) {
                    return $mid['id'] == $student->id;
                });
    
                $finalterm = collect($this->fintermAvg($subject,$section,$term))->first(function ($fin) use ($student) {
                    return $fin['id'] == $student->id;
                });


                if($subject->isLab > 0){
                    
                    $midtermLab = collect($this->midtermAvgLab($subject,$section,$term))->first(function ($mid) use ($student) {
                        return $mid['id'] == $student->id;
                    });

                    $finaltermLab = collect($this->fintermAvgLab($subject,$section,$term))->first(function ($fin) use ($student) {
                        return $fin['id'] == $student->id;
                    });
                    
                    $lectureWeight = ($mark ? $mark->lecture : 40) / 100;
                    $laboratoryWeight = ($mark ? $mark->laboratory : 60) / 100;
    
                    $lectotalGrade = ($midterm['grade'] + $finalterm['grade']) / 2;
                    $labtotalGrade = ($midtermLab['grade'] + $finaltermLab['grade']) / 2;
    
                    $roundedTotalGrade = $this->calculateRoundedGrade($lectureWeight * $lectotalGrade + $laboratoryWeight * $labtotalGrade);
                    $roundedTotalGradeLec = $this->calculateRoundedGrade($lectotalGrade);
                    $roundedTotalGradeLab = $this->calculateRoundedGrade($labtotalGrade);

                }else{
                    
                    $lectotalGrade = ($midterm['grade'] + $finalterm['grade']) / 2;
                    $roundedTotalGrade = $this->calculateRoundedGrade($lectotalGrade);
                    $roundedTotalGradeLec = $midterm['grade'];
                    $roundedTotalGradeLab = $finalterm['grade'];
                }

                $studentGrades[] = '';

                $gradesData[] = [
                    'id' => $student->id,
                    'Name' => $student->full_name,
                    ...$studentGrades,
                    'grade' => $roundedTotalGrade,
                    'midtermGrade' => $roundedTotalGradeLec,
                    'fintermGrade' => $roundedTotalGradeLab
                ];
            }else{
                $gradesData[] = [
                    'id' => $student->id,
                    'Name' => $student->full_name,
                    ...$studentGrades,
                    'grade' => number_format($roundedGrade, 2),
                ];

            }

            
        }

       
        return Inertia::render('Report/Grades',[
            'subjects' => $subject,
            'section' => $section,
            'user' => $user,
            'criteria' => $criteria,
            'students' => $gradesData,
            'tabs' => $tabs,
            'category' => $cat,
            'isLab' => $subject->isLab,
        ]);

        
    }

    public function calculateRoundedGrade($grade) {
        if ($grade > 3) {
            return round($grade * 2) / 2;
        } else {
            return round($grade * 4) / 4;
        }
    }

    public function midtermAvg($subject,$section,$term){
        $tabs = 'Midterm';
        $cat = 'lecture';

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);
        
        $students = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',)
        ->whereHas('registration', function($query) use ($subject, $section, $term){
            $query->whereHas('registration_details', function($query) use ($subject, $section, $term){
                $query->whereHas('schedule', function($query) use ($subject, $section, $term){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id)->where('term_id', $term);
                });
            });
        })
        ->orderBy('users.lname', 'ASC')
        ->get(['user_id']);

        $criteria = StudentGradeSub::
        whereHas('student_grade_cat', function ($query) use ($user, $tabs, $cat, $subject, $section) {
                $query->where('user_id', $user->id)->where('subject_id',$subject->id)->where('section_id',$section->id)->where('tabs', $tabs)->where('category', $cat);
            })
            ->orderBy('name', 'ASC')
            ->get(['id', 'name', 'over', 'student_grade_cat_id']);

        $gradesData = [];

        foreach ($students as $student) {
            $studentGrades = [];
            $categoryWeights = [];

            foreach ($criteria as $ctr) {
                $scores = StudentGradeScore::where('user_id', $student->id)
                    ->whereHas('student_grade_sub', function ($query) use ($ctr) {
                        $query->where('student_grade_sub_id', $ctr->id);
                    })
                    ->select('id', 'score')
                    ->get();

                $totalScore = $scores->sum('score');
                $perfectScore = $ctr->over;
                $percentage = $ctr->student_grade_cat->percentage;

                $averageScore = ($perfectScore > 0) ? (($totalScore / $perfectScore) * 100) : 0;

                $formattedAverageScore = number_format($averageScore, 2);

                $studentGrades[$ctr->name] = [
                    'id' => $ctr->id,
                    'category_id' => $ctr->student_grade_cat_id,
                    'percentage' => $percentage,
                    'over' => $ctr->over,
                    'total_score' => $totalScore,
                    'average_score' => $formattedAverageScore,
                ];

                // Store category weights
                if (!isset($categoryWeights[$ctr->student_grade_cat_id])) {
                    $categoryWeights[$ctr->student_grade_cat_id] = 0;
                }
                $categoryWeights[$ctr->student_grade_cat_id] = $percentage;
            }

           

            // Calculate the weighted average for each category
            $categoryAverages = [];
            foreach ($categoryWeights as $categoryId => $weight) {
                $catAvg = 0;
                $categoryTotalScore = 0;
                $categoryTotalPercentage = 0;
                //dd($studentGrades);
                foreach ($studentGrades as $grade) {
                    //dd($categoryId);
                    if ((int)$grade['category_id'] === $categoryId) {
                        //dd('true');
                        $categoryTotalScore += ($grade['average_score'] * $grade['percentage']);
                        $categoryTotalPercentage += $grade['percentage'];
                    }
                }

                $categoryAverage = ($categoryTotalPercentage > 0) ? ($categoryTotalScore / $categoryTotalPercentage) : 0;
                $catAvg = $categoryAverage * ($weight / 100);
                $categoryAverages[$categoryId] = number_format($catAvg, 2);

            }

            // Fill missing quizzes with 0 scores
            foreach ($criteria as $ctr) {
                if (!isset($studentGrades[$ctr->name])) {
                    $studentGrades[$ctr->name] = 0;
                }
            }

            $totalSum = 0;

            foreach ($categoryAverages as $categoryId => $formattedValue) {
                $numericValue = (float) str_replace(',', '', $formattedValue);
                $totalSum += $numericValue;
            }

            //calculate the over all percentage 
            //if $totalSum < 60%/Passing Rate 75-(25*(60-$totalSum)/60)
            //else 100-(25*(100-K13)/(100-G$2))


            $mark = StudentGradeMark::where('subject_id',$subject->id)
                    ->where('section_id', $section->id)
                    ->where('term_id', $term)
                    ->first();

            $passing = ($mark) ? $mark->passing : 60;
            $cutoff = ($mark) ? $mark->cutoff : 75;

            //dd($passing);

            if($totalSum < $passing){
                $percentage = 75 - (25*($passing-$totalSum)/$passing);
            }else{
                $percentage = 100 - (25*(100-$totalSum)/(100-$passing));
            }

            //convert it to a readable grade
            //1+2*((100-L13)/(100-$cutoff))
            //75% is the cut-off

            $readableGrade = 1 + 2 *((100-$percentage)/(100-$cutoff));

            //rounded grade
            //=IF($readableGrade>3,MROUND($readableGrade,0.5),MROUND($readableGrade,0.25))
            $roundedGrade = 0;
            
            if ($readableGrade > 3) {
                $roundedGrade = round($readableGrade * 2) / 2;
            } else {
                $roundedGrade = round($readableGrade * 4) / 4;
            }

            $gradesData[] = [
                'id' => $student->id,
                'grade' => number_format($roundedGrade, 2),
            ];

        }

        return $gradesData;
    }

    public function midtermAvgLab($subject,$section,$term){
        $tabs = 'Midterm';
        $cat = 'laboratory';

        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);
        
        $students = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',)
        ->whereHas('registration', function($query) use ($subject, $section, $term){
            $query->whereHas('registration_details', function($query) use ($subject, $section, $term){
                $query->whereHas('schedule', function($query) use ($subject, $section, $term){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id)->where('term_id', $term);
                });
            });
        })
        ->orderBy('users.lname', 'ASC')
        ->get(['user_id']);

        $criteria = StudentGradeSub::
        whereHas('student_grade_cat', function ($query) use ($user, $tabs, $cat, $subject, $section) {
                $query->where('user_id', $user->id)->where('subject_id',$subject->id)->where('section_id',$section->id)->where('tabs', $tabs)->where('category', $cat);
            })
            ->orderBy('name', 'ASC')
            ->get(['id', 'name', 'over', 'student_grade_cat_id']);

        $gradesData = [];

        foreach ($students as $student) {
            $studentGrades = [];
            $categoryWeights = [];

            foreach ($criteria as $ctr) {
                $scores = StudentGradeScore::where('user_id', $student->id)
                    ->whereHas('student_grade_sub', function ($query) use ($ctr) {
                        $query->where('student_grade_sub_id', $ctr->id);
                    })
                    ->select('id', 'score')
                    ->get();

                $totalScore = $scores->sum('score');
                $perfectScore = $ctr->over;
                $percentage = $ctr->student_grade_cat->percentage;

                $averageScore = ($perfectScore > 0) ? (($totalScore / $perfectScore) * 100) : 0;

                $formattedAverageScore = number_format($averageScore, 2);

                $studentGrades[$ctr->name] = [
                    'id' => $ctr->id,
                    'category_id' => $ctr->student_grade_cat_id,
                    'percentage' => $percentage,
                    'over' => $ctr->over,
                    'total_score' => $totalScore,
                    'average_score' => $formattedAverageScore,
                ];

                // Store category weights
                if (!isset($categoryWeights[$ctr->student_grade_cat_id])) {
                    $categoryWeights[$ctr->student_grade_cat_id] = 0;
                }
                $categoryWeights[$ctr->student_grade_cat_id] = $percentage;
            }

           

            // Calculate the weighted average for each category
            $categoryAverages = [];
            foreach ($categoryWeights as $categoryId => $weight) {
                $catAvg = 0;
                $categoryTotalScore = 0;
                $categoryTotalPercentage = 0;
                //dd($studentGrades);
                foreach ($studentGrades as $grade) {
                    //dd($categoryId);
                    if ((int)$grade['category_id'] === $categoryId) {
                        //dd('true');
                        $categoryTotalScore += ($grade['average_score'] * $grade['percentage']);
                        $categoryTotalPercentage += $grade['percentage'];
                    }
                }

                $categoryAverage = ($categoryTotalPercentage > 0) ? ($categoryTotalScore / $categoryTotalPercentage) : 0;
                $catAvg = $categoryAverage * ($weight / 100);
                $categoryAverages[$categoryId] = number_format($catAvg, 2);

            }

            // Fill missing quizzes with 0 scores
            foreach ($criteria as $ctr) {
                if (!isset($studentGrades[$ctr->name])) {
                    $studentGrades[$ctr->name] = 0;
                }
            }

            $totalSum = 0;

            foreach ($categoryAverages as $categoryId => $formattedValue) {
                $numericValue = (float) str_replace(',', '', $formattedValue);
                $totalSum += $numericValue;
            }

            //calculate the over all percentage 
            //if $totalSum < 60%/Passing Rate 75-(25*(60-$totalSum)/60)
            //else 100-(25*(100-K13)/(100-G$2))

            $mark = StudentGradeMark::where('subject_id',$subject->id)
                    ->where('section_id', $section->id)
                    ->where('term_id', $term)
                    ->first();

            $passing = ($mark) ? $mark->passing : 60;
            $cutoff = ($mark) ? $mark->cutoff : 75;

            //dd($passing);

            if($totalSum < $passing){
                $percentage = 75 - (25*($passing-$totalSum)/$passing);
            }else{
                $percentage = 100 - (25*(100-$totalSum)/(100-$passing));
            }

            //convert it to a readable grade
            //1+2*((100-L13)/(100-$cutoff))
            //75% is the cut-off

            $readableGrade = 1 + 2 *((100-$percentage)/(100-$cutoff));

            //rounded grade
            //=IF($readableGrade>3,MROUND($readableGrade,0.5),MROUND($readableGrade,0.25))
            $roundedGrade = 0;
            
            if ($readableGrade > 3) {
                $roundedGrade = round($readableGrade * 2) / 2;
            } else {
                $roundedGrade = round($readableGrade * 4) / 4;
            }

            $gradesData[] = [
                'id' => $student->id,
                'grade' => number_format($roundedGrade, 2),
            ];

        }

        return $gradesData;
    }

    public function fintermAvg($subject,$section,$term){
        $tabs = 'Finals';
        $cat = 'lecture';
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);
        
        $students = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',)
        ->whereHas('registration', function($query) use ($subject, $section, $term){
            $query->whereHas('registration_details', function($query) use ($subject, $section, $term){
                $query->whereHas('schedule', function($query) use ($subject, $section, $term){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id)->where('term_id', $term);
                });
            });
        })
        ->orderBy('users.lname', 'ASC')
        ->get(['user_id']);

        $criteria = StudentGradeSub::
        whereHas('student_grade_cat', function ($query) use ($user, $tabs, $cat, $subject, $section) {
                $query->where('user_id', $user->id)->where('subject_id',$subject->id)->where('section_id',$section->id)->where('tabs', $tabs)->where('category', $cat);
            })
            ->orderBy('name', 'ASC')
            ->get(['id', 'name', 'over', 'student_grade_cat_id']);

        $gradesData = [];

        foreach ($students as $student) {
            $studentGrades = [];
            $categoryWeights = [];

            foreach ($criteria as $ctr) {
                $scores = StudentGradeScore::where('user_id', $student->id)
                    ->whereHas('student_grade_sub', function ($query) use ($ctr) {
                        $query->where('student_grade_sub_id', $ctr->id);
                    })
                    ->select('id', 'score')
                    ->get();

                $totalScore = $scores->sum('score');
                $perfectScore = $ctr->over;
                $percentage = $ctr->student_grade_cat->percentage;

                $averageScore = ($perfectScore > 0) ? (($totalScore / $perfectScore) * 100) : 0;

                $formattedAverageScore = number_format($averageScore, 2);

                $studentGrades[$ctr->name] = [
                    'id' => $ctr->id,
                    'category_id' => $ctr->student_grade_cat_id,
                    'percentage' => $percentage,
                    'over' => $ctr->over,
                    'total_score' => $totalScore,
                    'average_score' => $formattedAverageScore,
                ];

                // Store category weights
                if (!isset($categoryWeights[$ctr->student_grade_cat_id])) {
                    $categoryWeights[$ctr->student_grade_cat_id] = 0;
                }
                $categoryWeights[$ctr->student_grade_cat_id] = $percentage;
            }

           

            // Calculate the weighted average for each category
            $categoryAverages = [];
            foreach ($categoryWeights as $categoryId => $weight) {
                $catAvg = 0;
                $categoryTotalScore = 0;
                $categoryTotalPercentage = 0;
                //dd($studentGrades);
                foreach ($studentGrades as $grade) {
                    //dd($categoryId);
                    if ((int)$grade['category_id'] === $categoryId) {
                        //dd('true');
                        $categoryTotalScore += ($grade['average_score'] * $grade['percentage']);
                        $categoryTotalPercentage += $grade['percentage'];
                    }
                }

                $categoryAverage = ($categoryTotalPercentage > 0) ? ($categoryTotalScore / $categoryTotalPercentage) : 0;
                $catAvg = $categoryAverage * ($weight / 100);
                $categoryAverages[$categoryId] = number_format($catAvg, 2);

            }

            // Fill missing quizzes with 0 scores
            foreach ($criteria as $ctr) {
                if (!isset($studentGrades[$ctr->name])) {
                    $studentGrades[$ctr->name] = 0;
                }
            }

            $totalSum = 0;

            foreach ($categoryAverages as $categoryId => $formattedValue) {
                $numericValue = (float) str_replace(',', '', $formattedValue);
                $totalSum += $numericValue;
            }

            //calculate the over all percentage 
            //if $totalSum < 60%/Passing Rate 75-(25*(60-$totalSum)/60)
            //else 100-(25*(100-K13)/(100-G$2))

            $mark = StudentGradeMark::where('subject_id',$subject->id)
                    ->where('section_id', $section->id)
                    ->where('term_id', $term)
                    ->first();

            $passing = ($mark) ? $mark->passing : 60;
            $cutoff = ($mark) ? $mark->cutoff : 75;

            //dd($passing);

            if($totalSum < $passing){
                $percentage = 75 - (25*($passing-$totalSum)/$passing);
            }else{
                $percentage = 100 - (25*(100-$totalSum)/(100-$passing));
            }

            //convert it to a readable grade
            //1+2*((100-L13)/(100-$cutoff))
            //75% is the cut-off

            $readableGrade = 1 + 2 *((100-$percentage)/(100-$cutoff));

            //rounded grade
            //=IF($readableGrade>3,MROUND($readableGrade,0.5),MROUND($readableGrade,0.25))
            $roundedGrade = 0;
            
            if ($readableGrade > 3) {
                $roundedGrade = round($readableGrade * 2) / 2;
            } else {
                $roundedGrade = round($readableGrade * 4) / 4;
            }

            $gradesData[] = [
                'id' => $student->id,
                'grade' => number_format($roundedGrade, 2),
            ];

        }

        return $gradesData;
    }

    public function fintermAvgLab($subject,$section,$term){
        $tabs = 'Finals';
        $cat = 'laboratory';
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);
        
        $students = User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id',)
        ->whereHas('registration', function($query) use ($subject, $section, $term){
            $query->whereHas('registration_details', function($query) use ($subject, $section, $term){
                $query->whereHas('schedule', function($query) use ($subject, $section, $term){
                    $query->where('section_id', $section->id)->where('subject_id', $subject->id)->where('term_id', $term);
                });
            });
        })
        ->orderBy('users.lname', 'ASC')
        ->get(['user_id']);

        $criteria = StudentGradeSub::
        whereHas('student_grade_cat', function ($query) use ($user, $tabs, $cat, $subject, $section) {
                $query->where('user_id', $user->id)->where('subject_id',$subject->id)->where('section_id',$section->id)->where('tabs', $tabs)->where('category', $cat);
            })
            ->orderBy('name', 'ASC')
            ->get(['id', 'name', 'over', 'student_grade_cat_id']);

        $gradesData = [];

        foreach ($students as $student) {
            $studentGrades = [];
            $categoryWeights = [];

            foreach ($criteria as $ctr) {
                $scores = StudentGradeScore::where('user_id', $student->id)
                    ->whereHas('student_grade_sub', function ($query) use ($ctr) {
                        $query->where('student_grade_sub_id', $ctr->id);
                    })
                    ->select('id', 'score')
                    ->get();

                $totalScore = $scores->sum('score');
                $perfectScore = $ctr->over;
                $percentage = $ctr->student_grade_cat->percentage;

                $averageScore = ($perfectScore > 0) ? (($totalScore / $perfectScore) * 100) : 0;

                $formattedAverageScore = number_format($averageScore, 2);

                $studentGrades[$ctr->name] = [
                    'id' => $ctr->id,
                    'category_id' => $ctr->student_grade_cat_id,
                    'percentage' => $percentage,
                    'over' => $ctr->over,
                    'total_score' => $totalScore,
                    'average_score' => $formattedAverageScore,
                ];

                // Store category weights
                if (!isset($categoryWeights[$ctr->student_grade_cat_id])) {
                    $categoryWeights[$ctr->student_grade_cat_id] = 0;
                }
                $categoryWeights[$ctr->student_grade_cat_id] = $percentage;
            }

           

            // Calculate the weighted average for each category
            $categoryAverages = [];
            foreach ($categoryWeights as $categoryId => $weight) {
                $catAvg = 0;
                $categoryTotalScore = 0;
                $categoryTotalPercentage = 0;
                //dd($studentGrades);
                foreach ($studentGrades as $grade) {
                    //dd($categoryId);
                    if ((int)$grade['category_id'] === $categoryId) {
                        //dd('true');
                        $categoryTotalScore += ($grade['average_score'] * $grade['percentage']);
                        $categoryTotalPercentage += $grade['percentage'];
                    }
                }

                $categoryAverage = ($categoryTotalPercentage > 0) ? ($categoryTotalScore / $categoryTotalPercentage) : 0;
                $catAvg = $categoryAverage * ($weight / 100);
                $categoryAverages[$categoryId] = number_format($catAvg, 2);

            }

            // Fill missing quizzes with 0 scores
            foreach ($criteria as $ctr) {
                if (!isset($studentGrades[$ctr->name])) {
                    $studentGrades[$ctr->name] = 0;
                }
            }

            $totalSum = 0;

            foreach ($categoryAverages as $categoryId => $formattedValue) {
                $numericValue = (float) str_replace(',', '', $formattedValue);
                $totalSum += $numericValue;
            }

            //calculate the over all percentage 
            //if $totalSum < 60%/Passing Rate 75-(25*(60-$totalSum)/60)
            //else 100-(25*(100-K13)/(100-G$2))

            $mark = StudentGradeMark::where('subject_id',$subject->id)
                    ->where('section_id', $section->id)
                    ->where('term_id', $term)
                    ->first();

            $passing = ($mark) ? $mark->passing : 60;
            $cutoff = ($mark) ? $mark->cutoff : 75;

            //dd($passing);

            if($totalSum < $passing){
                $percentage = 75 - (25*($passing-$totalSum)/$passing);
            }else{
                $percentage = 100 - (25*(100-$totalSum)/(100-$passing));
            }

            //convert it to a readable grade
            //1+2*((100-L13)/(100-$cutoff))
            //75% is the cut-off

            $readableGrade = 1 + 2 *((100-$percentage)/(100-$cutoff));

            //rounded grade
            //=IF($readableGrade>3,MROUND($readableGrade,0.5),MROUND($readableGrade,0.25))
            $roundedGrade = 0;
            
            if ($readableGrade > 3) {
                $roundedGrade = round($readableGrade * 2) / 2;
            } else {
                $roundedGrade = round($readableGrade * 4) / 4;
            }

            $gradesData[] = [
                'id' => $student->id,
                'grade' => number_format($roundedGrade, 2),
            ];

        }

        return $gradesData;
    }


    public function upsert(){
        $stud = request()->stud;
        $scoreId = request()->scoreId;
        $score = request()->score;
        $term = getSelectedTerm();

         $data = [
             'user_id' => $stud,
             'student_grade_sub_id' => $scoreId,
             'term_id' => $term->id
         ];
 
         $existingRecord = StudentGradeScore::where($data)->first();
 
         if ($existingRecord) {

             $existingRecord->score = $score;
             $existingRecord->save();
         } else {

             StudentGradeScore::create(array_merge($data, ['score' => $score]));
         }
 
         //DB::statement('SET IDENTITY_INSERT student_grade_scores OFF');
    }

    public function criteria(Subject $subject, Section $section, $term){
        $user =  User::select(DB::raw("lname + ', ' + fname + ISNULL(' ' + SUBSTRING(mname, 1, 1) + '.', '') + ISNULL(' ' + extname, '') as full_name"),'users.id')->find(auth()->user()->id);

        $categoriesMidLec = StudentGradeCat::with('student_grade_sub')
            ->where('subject_id',$subject->id)
            ->where('section_id',$section->id)
            ->where('tabs','Midterm')
            ->where('category', 'Lecture')
            ->where('user_id', $user->id)
            ->orderBy('position','ASC')
            ->get();
        
        $categoriesFinLec = StudentGradeCat::with('student_grade_sub')
            ->where('subject_id',$subject->id)
            ->where('section_id',$section->id)
            ->where('tabs','Finals')
            ->where('category', 'Lecture')
            ->where('user_id', $user->id)
            ->orderBy('position','ASC')
            ->get();

        $categoriesMidLab = StudentGradeCat::with('student_grade_sub')
            ->where('subject_id',$subject->id)
            ->where('section_id',$section->id)
            ->where('tabs','Midterm')
            ->where('category', 'Laboratory')
            ->where('user_id', $user->id)
            ->orderBy('position','ASC')
            ->get();
        
        $categoriesFinLab = StudentGradeCat::with('student_grade_sub')
            ->where('subject_id',$subject->id)
            ->where('section_id',$section->id)
            ->where('tabs','Finals')
            ->where('category', 'Laboratory')
            ->where('user_id', $user->id)
            ->orderBy('position','ASC')
            ->get();
        

        $mark = StudentGradeMark::where('subject_id',$subject->id)
                ->where('section_id', $section->id)
                ->where('term_id', $term)
                ->first();

        $passing = ($mark) ? $mark->passing : 60;
        $cutoff = ($mark) ? $mark->cutoff : 75;

        return Inertia::render('Report/Criteria',[
            'subjects' => $subject,
            'section' => $section,
            'user' => $user,
            'categoriesMidLec' => $categoriesMidLec,
            'categoriesFinLec' => $categoriesFinLec,
            'categoriesMidLab' => $categoriesMidLab,
            'categoriesFinLab' => $categoriesFinLab,
            'marks' => $mark,
            'isLab' => $subject->isLab,
        ]);
    }

    public function subcategory(){
        $existingSub = StudentGradeSub::where('name', request()->name)
            ->where('student_grade_cat_id', request()->student_grade_cat_id)
            ->first();

        if ($existingSub) {
            return redirect()->back()->with('error', 'Sub Category with the same name already exists.');
        }else{
            $result = StudentGradeSub::create(array_merge($this->validateSubData(), [
                'name' => request()->name,
                'student_grade_cat_id' => request()->student_grade_cat_id,
                'over' => request()->over,
            ]));
    
            return redirect()->back()->with('success', 'Sub-Category has been successfully created.');
        }

    }

    public function editSub(StudentGradeSub $student_grade_sub){
        $attributes = $this->validateSubData($student_grade_sub);
         // Check for duplicate name
        $existingSub = StudentGradeSub::where('name', $attributes['name'])
            ->where('id', '!=', $student_grade_sub->id)
            ->where('student_grade_cat_id', $attributes['student_grade_cat_id'])
            ->first();

        if ($existingSub) {
            return redirect()->back()->with('error', 'Sub Category with the same name already exists.');
        }else{
            $student_grade_sub->update($attributes);
            return redirect()->back()->with('success', 'Sub-Category has been successfully updated.');
        }
           
    }

    public function editCat(StudentGradeCat $student_grade_cat){
        $attributes = $this->validateData($student_grade_cat);

       // dd($attributes);
        
        $categories = StudentGradeCat::where('id', '!=', $student_grade_cat->id)
            ->where('subject_id',$attributes['subject_id'])
            ->where('section_id',$attributes['section_id'])
            ->where('tabs',$attributes['tabs'])
            ->where('category', $attributes['category'])
            ->where('user_id', $student_grade_cat->user_id)
            ->orderBy('id','ASC')
            ->sum('percentage');

        $totalSum = $attributes['percentage'] + $categories;

        $existingCat = StudentGradeCat::where('name', 'LIKE', $attributes['name'])
            ->where('subject_id',$attributes['subject_id'])
            ->where('section_id',$attributes['section_id'])
            ->where('id', '!=', $student_grade_cat->id)
            ->where('tabs','LIKE', $attributes['tabs'])
            ->where('category', $attributes['category'])
            ->where('user_id', $student_grade_cat->user_id)
            ->first();

        if ($existingCat) {
            // Duplicate name found
            return redirect()->back()->with('error', 'Category with the same name already exists.');
        }

       if($totalSum>100){
            return redirect()->back()->with('error', 'The percentage was exceeded to 100%');
       }

        $student_grade_cat->update($attributes);
        return redirect()->back()->with('success', 'Category has been successfully updated.');
    }

    public function category(){
        
        $user = auth()->user()->id;

        $attributes = array_merge(
            $this->validateData(),
            [
                'name' => request()->name,
                'section_id' => request()->section_id,
                'subject_id' => request()->subject_id,
                'percentage' => request()->percentage,
                'tabs' => request()->tabs,
                'category' => request()->category,
                'user_id' => $user,
            ]
        );

        $conditions = [
            'name' => $attributes['name'],
            'section_id' => $attributes['section_id'],
            'subject_id' => $attributes['subject_id'],
            'tabs' => $attributes['tabs'],
            'category' => $attributes['category'],
            'user_id' => $user,
        ];

         // Check for duplicate name (case-insensitive)
        $existingCat = StudentGradeCat::where($conditions)
        ->first();


        if ($existingCat) {
            // Duplicate name found
            return redirect()->back()->with('error', 'Category with the same name already exists.');
        }

        $result = StudentGradeCat::create(array_merge($this->validateData(), [
            'name' => request()->name,
            'section_id' => request()->section_id,
            'subject_id' => request()->subject_id,
            'percentage' => request()->percentage,
            'tabs' => request()->tabs,
            'category' => request()->category,
            'user_id' => $user,
        ]));

        if ($result) {
            return redirect()->back()->with('success', 'Category successfully created');
        } else {
            return redirect()->back()->with('error', 'Failed to create category');
        }
    }

    public function destroySub($id){
        StudentGradeSub::where('id', '=', $id)->delete();
    }

    public function destroyCat($id){
        StudentGradeCat::where('id', '=', $id)->delete();
    }

    protected function validateData(?StudentGradeCat $cat = null): array
    {
        $cat ??= new StudentGradeCat();
        return request()->validate([
            'name' => 'required',
            'percentage' => 'required',
            'tabs' => 'required',
            'category' => 'required',
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'section_id' => 'required',
            'user_id' => 'nullable',
        ]);
    }

    protected function validateSubData(?StudentGradeSub $sub = null): array
    {
        $sub ??= new StudentGradeSub();
        return request()->validate([
            'name' => 'required',
            'student_grade_cat_id' => 'required',
            'over' => 'required'
        ]);
    }

    public function mark(){

        $attributes = array_merge(
            $this->validateMark(),
            [
                'lecture' => request()->lecture,
                'laboratory' => request()->laboratory,
                'cutoff' => request()->cutoff,
                'passing' => request()->passing,
                'subject_id' => request()->subject_id,
                'section_id' => request()->section_id,
                'user_id' => request()->user_id,
                'term_id' => request()->term_id,
            ]
        );

        $conditions = [
            'subject_id' => $attributes['subject_id'],
            'section_id' => $attributes['section_id'],
            'term_id' => $attributes['term_id'],
        ];
        
        // Check if a record with the specified conditions exists
        $mark = StudentGradeMark::where($conditions)->first();
        
        if ($mark) {
            // If the record exists, update it
            $mark->update($attributes);
            return redirect()->back()->with('success', 'Successfully Updated!');
        } else {
            // If the record does not exist, create a new one
            $result = StudentGradeMark::create($attributes);
            return redirect()->back()->with('success', 'Successfully Updated!');
        }
        

    }

    protected function validateMark(?StudentGradeMark $mark = null): array
    {
        $mark ??= new StudentGradeMark();
        return request()->validate([
            'lecture' => 'nullable',
            'laboratory' => 'nullable',
            'cutoff' => 'required',
            'passing' => 'required',
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'section_id' => ['required', Rule::exists('sections', 'id')],
            'user_id' => ['required', Rule::exists('users', 'id')],
            'term_id' => ['required', Rule::exists('terms', 'id')],
        ]);
    }

    public function sort(){
        $positions = StudentGradeCat::all();

        foreach ($positions as $position){
            $id = $position['id'];
            foreach (request()->position as $post){
                if($post['id']==$id){
                    $position->update(['position' => $post['position']]);
                }
            }
        }

        return redirect()->back();
    }

}
