<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\v1\Questions\ClaimQuestionMailRequest;
use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use App\Http\Controllers\Controller;
use App\Models\Subtopic;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\v1\Questions\CreateQuestionRequest;
use App\Http\Requests\Api\v1\Questions\UpdateQuestionRequest;
use App\Http\Requests\Api\v1\Questions\ActionForMassiveSelectionQuestionsRequest;
use App\Http\Requests\Api\v1\Questions\ExportQuestionsRequest;
use App\Http\Requests\Api\v1\Questions\ImportQuestionsRequest;

class QuestionsController extends Controller
{
    protected QuestionsInterface $questionsInterface;

    public function __construct(QuestionsInterface $questionsInterface ){
        $this->questionsInterface = $questionsInterface;
    }

    public function subtopics_relationship_get_questions(Subtopic $subtopic)
    {
        return $this->questionsInterface->subtopics_relationship_get_questions($subtopic);
    }

    public function subtopic_relationship_questions_read( Subtopic $subtopic, Question $question ) {
        return $this->questionsInterface->subtopic_relationship_questions_read( $subtopic, $question );
    }
    public function subtopic_relationship_questions_create( CreateQuestionRequest $request, Subtopic $subtopic, Question $question ) {
        return $this->questionsInterface->subtopic_relationship_questions_create( $request, $subtopic, $question );
    }
    public function subtopic_relationship_questions_update( UpdateQuestionRequest $request, Subtopic $subtopic, Question $question ) {
        return $this->questionsInterface->subtopic_relationship_questions_update( $request, $subtopic, $question );
    }
    public function subtopic_relationship_questions_delete( Subtopic $subtopic, Question $question ) {
        return $this->questionsInterface->subtopic_relationship_questions_delete( $subtopic, $question );
    }

    public function topics_relationship_get_questions(Topic $topic) {
        return $this->questionsInterface->topics_relationship_get_questions($topic);
    }
    public function topic_relationship_questions_read( Topic $topic, Question $question ) {

        \Log::debug('QuestionsController->topic_relationship_questions_read');

        return $this->questionsInterface->topic_relationship_questions_read( $topic, $question );
    }
    public function topic_relationship_questions_create( CreateQuestionRequest $request, Topic $topic, Question $question ) {
        return $this->questionsInterface->topic_relationship_questions_create( $request, $topic, $question );
    }
    public function topic_relationship_questions_update( UpdateQuestionRequest $request, Topic $topic, Question $question ) {
        return $this->questionsInterface->topic_relationship_questions_update( $request, $topic, $question );
    }
    public function topic_relationship_questions_delete( Topic $topic, Question $question ) {
        return $this->questionsInterface->topic_relationship_questions_delete( $topic, $question );
    }

    public function claim_question_mail( ClaimQuestionMailRequest $request ) {
        return $this->questionsInterface->claim_question_mail( $request );
    }

    public function import_records(ImportQuestionsRequest $request){
        return $this->questionsInterface->import_records( $request );
    }

    public function set_mode_edit_question( Request $request, Question $question ) {
        $request->validate([
            'is-mode-edition-question' => ['required', 'in:yes,no']
        ]);

        return $this->questionsInterface->set_mode_edit_question( $request, $question );
    }

    public function set_state_visibility_question( Request $request, Question $question ) {
        $request->validate([
            'is-visible-question' => ['required', 'in:yes,no']
        ]);

        $question->is_visible = $request->get('is-visible-question');
        $question->save();

        return response()->json([
            'message' => 'successfully'
        ]);
    }

    public function download_template_import_records (): \Symfony\Component\HttpFoundation\StreamedResponse {
        return Storage::disk('public')->download('templates/csv/questions_import.csv', 'template_import_questions');
    }
}
