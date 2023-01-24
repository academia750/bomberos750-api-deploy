<?php
namespace App\Core\Resources\Questions\v1;

use App\Core\Resources\Questions\v1\Services\ClaimQuestionMail;
use App\Core\Resources\Questions\v1\Services\SaveQuestionsService;
use App\Core\Services\ManageImagesStorage;
use App\Imports\Api\v1\QuestionsImport;
use App\Models\Answer;
use App\Models\Question;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
//use App\Imports\Api\Questions\v1\QuestionsImport;
use App\Exports\Api\Questions\v1\QuestionsExport;


class DBApp implements QuestionsInterface
{
    protected Question $model;

    public function __construct(Question $question ){
        $this->model = $question;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {

        $questions_id = $subtopic->questions()->pluck("id");

        return $this->model->query()->whereIn('id', $questions_id->toArray())->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        return $subtopic->questions()->applyIncludes()->firstWhere("id", "=", $question->getRouteKey());
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        $question = SaveQuestionsService::saveQuestion($request, $subtopic);

        if ($request->file('file-reason')) {
            SaveQuestionsService::saveImageFileQuestion($request, $question, 'public/questions/images/subtopics');
        }

        foreach (SaveQuestionsService::getAnswersByQuestion($request, $question) as $answerData) {
            Answer::query()->create([
                'answer' => $answerData["answer"],
                'is_grouper_answer' => $answerData["is_grouper_answer"],
                'is_correct_answer' => $answerData["is_correct_answer"],
                'question_id' => $answerData["question_id"],
            ]);
        }

        return $this->model->query()->applyIncludes()->find($question->getRouteKey());
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        // Delete Alternatives

        $question = Question::query()->findOrFail($question->id);

        foreach ($question->answers()->pluck('id')->toArray() as $answer_id) {
            $answer = Answer::query()->findOrFail($answer_id);
            $answer->delete();
        }

        $question = SaveQuestionsService::updateQuestion($request, $question);

        if ($request->get('remove-image-existing') === 'yes') {
            $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel($question, "/");

            ManageImagesStorage::deleteImageStorage($nameFileStorage);

            $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel($question, "\\");

            ManageImagesStorage::deleteImageStorage($nameFileStorage);
        }

        if ($request->file('file-reason') && $request->get('remove-image-existing') === 'no') {
            SaveQuestionsService::updateImageQuestionInStorage($request, $question, 'public/questions/images/subtopics');
        }

        foreach (SaveQuestionsService::getAnswersByQuestion($request, $question) as $answerData) {
            Answer::query()->create([
                'answer' => $answerData["answer"],
                'is_grouper_answer' => $answerData["is_grouper_answer"],
                'is_correct_answer' => $answerData["is_correct_answer"],
                'question_id' => $answerData["question_id"],
            ]);
        }

        return $this->model->query()->applyIncludes()->find($question->getRouteKey());

    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {
        try {
            DB::beginTransaction();

                $question->delete();

            DB::commit();

            return "Successfully";
        } catch (\Exception $e) {
            DB::rollback();
            \Log::debug($e->getMessage());
            abort(500,$e->getMessage());
        }
    }

    public function topics_relationship_get_questions($topic)
    {
        //$questions_id = $topic->questions()->pluck("id");


        if (request()?->query('filter') && request()?->query('filter')['search']) {
            $questions_id = DB::select(
                "call search_question_in_topics_and_subtopics(?,?)",
                array(request()?->query('filter')['search'], $topic->getRouteKey())
            ); //search_question_in_topics_and_subtopics


            $questions_id = collect($questions_id)->pluck('id')->toArray();

            return Question::query()->whereIn('id', $questions_id)->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
        }

        return $topic->questions()->applyFilters()->applySorts()->applyIncludes()->jsonPaginate();
    }

    public function topic_relationship_questions_read($topic, $question)
    {
        return $topic->questions()->applyIncludes()->firstWhere("id", "=", $question->getRouteKey());
    }

    public function topic_relationship_questions_create($request, $topic)
    {
        $question = SaveQuestionsService::saveQuestion($request, $topic);

        if ($request->file('file-reason')) {
            SaveQuestionsService::saveImageFileQuestion($request, $question, 'public/questions/images/topics');
        }

        foreach (SaveQuestionsService::getAnswersByQuestion($request, $question) as $answerData) {
            Answer::query()->create([
                'answer' => $answerData["answer"],
                'is_grouper_answer' => $answerData["is_grouper_answer"],
                'is_correct_answer' => $answerData["is_correct_answer"],
                'question_id' => $answerData["question_id"],
            ]);
        }

        return $this->model->query()->applyIncludes()->find($question->getRouteKey());
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {

        // Delete Alternatives

        $question = Question::query()->findOrFail($question->id);

        foreach ($question->answers()->pluck('id')->toArray() as $answer_id) {
            $answer = Answer::query()->findOrFail($answer_id);
            $answer->delete();
        }

        $question = SaveQuestionsService::updateQuestion($request, $question);

        if ($request->get('remove-image-existing') === 'yes') {
            $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel($question, "/");

            ManageImagesStorage::deleteImageStorage($nameFileStorage);

            $nameFileStorage = ManageImagesStorage::getPathForDeleteImageModel($question, "\\");

            ManageImagesStorage::deleteImageStorage($nameFileStorage);
        }

        if ($request->file('file-reason') && $request->get('remove-image-existing') === 'no') {
            SaveQuestionsService::updateImageQuestionInStorage($request, $question, 'public/questions/images/topics');
        }

        foreach (SaveQuestionsService::getAnswersByQuestion($request, $question) as $answerData) {
            Answer::query()->create([
                'answer' => $answerData["answer"],
                'is_grouper_answer' => $answerData["is_grouper_answer"],
                'is_correct_answer' => $answerData["is_correct_answer"],
                'question_id' => $answerData["question_id"],
            ]);
        }

        return $this->model->query()->applyIncludes()->find($question->getRouteKey());
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        try {
            DB::beginTransaction();
            $question->delete();

            DB::commit();

            return "Successfully";
        } catch (\Exception $e) {
            DB::rollback();
            abort(500,$e->getMessage());
        }
    }


    public function claim_question_mail($request)
    {
        try {

            ClaimQuestionMail::claimQuestion(
                $request->get('test_id'),
                $request->get('question_id'),
                $request->get('claim_text'),
            );

            return "Successfully";
        } catch (\Exception $e) {
            //DB::rollback();
            abort(500,$e->getMessage());
        }
    }

    public function import_records($request)
    {
        //$outputArtisan = Artisan::output();

        /*\Log::debug(strpos($outputArtisan, 'queue:work') !== false);
        \Log::debug(str_contains($outputArtisan, 'queue:work'));

        if (strpos($outputArtisan, 'queue:work') !== false) {
            $filesQuestions = $request->file('filesQuestions') ?? [];

            foreach ($filesQuestions as $file) {

                (
                new QuestionsImport(Auth::user(), $file->getClientOriginalName())
                )->import($file);

                //sleep(1);

            }

            return 'success';
        }

        return 'failed';*/

        $filesQuestions = $request->file('filesQuestions') ?? [];

        foreach ($filesQuestions as $file) {

            (
            new QuestionsImport(Auth::user(), $file->getClientOriginalName())
            )->import($file);

            //sleep(1);

        }

        return 'success';
    }
}
