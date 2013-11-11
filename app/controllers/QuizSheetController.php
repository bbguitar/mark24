<?php //-->

class QuizSheetController extends BaseController
{
    public function __construct() {
        $this->beforeFilter('are-you-a-student');
    }
    
    public function index($quizId)
    {
        // get quiz details
        $quiz = Quiz::find($quizId);
        // check if the user already answered the quiz
        $alreadyTaken = QuizTaker::where('user_id', '=', Auth::user()->id)
            ->where('status', '=', 'PASSED')
            ->first();

        // validate if quiz is empty
        if(empty($quiz)) {
            // redirect to 404
            return Redirect::to('/pagenotfound');
        }
        
        if(!empty($alreadyTaken)) {
            return Redirect::to('/pagenotfound');
        }

        // get the questions
        $questions = QuestionList::getQuizQuestions($quizId);
        // get the details of the user who assigned the quiz
        $assigned = User::find($quiz->user_id);

        // show quiz sheet page
        return View::make('quizsheet.index')
            ->with('quiz', $quiz)
            ->with('questions', $questions)
            ->with('assigned', $assigned);
    }
}
