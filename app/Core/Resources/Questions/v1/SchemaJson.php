<?php
namespace App\Core\Resources\Questions\v1;

use App\Http\Resources\Api\Subtopic\v1\SubtopicResource;
use App\Http\Resources\Api\Topic\v1\TopicResource;
use App\Core\Resources\Questions\v1\Interfaces\QuestionsInterface;
use App\Http\Resources\Api\Question\v1\QuestionCollection;
use App\Http\Resources\Api\Question\v1\QuestionResource;

class SchemaJson implements QuestionsInterface
{
    protected EventApp $eventApp;

    public function __construct(EventApp $eventApp ){
        $this->eventApp = $eventApp;
    }

    public function subtopics_relationship_get_questions($subtopic)
    {
        return QuestionCollection::make(
            $this->eventApp->subtopics_relationship_get_questions($subtopic)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic),
            ]
        ]);
    }

    public function subtopic_relationship_questions_read($subtopic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->subtopic_relationship_questions_read($subtopic, $question)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic)
            ]
        ]);
    }

    public function subtopic_relationship_questions_create($request, $subtopic)
    {
        return QuestionResource::make(
            $this->eventApp->subtopic_relationship_questions_create($request, $subtopic)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic)
            ]
        ]);
    }

    public function subtopic_relationship_questions_update($request, $subtopic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->subtopic_relationship_questions_update($request, $subtopic, $question)
        )->additional([
            'meta' => [
                'subtopic' => SubtopicResource::make($subtopic)
            ]
        ]);
    }

    public function subtopic_relationship_questions_delete($subtopic, $question)
    {
        $this->eventApp->subtopic_relationship_questions_delete($subtopic, $question);

        return response()->noContent();
    }

    public function topics_relationship_get_questions($topic)
    {
        $total_questions_subtopics = 0;

        foreach ($topic->subtopics as $subtopic) {
            $total_questions_subtopics+=$subtopic->questions->count();
        }

        return QuestionCollection::make(
            $this->eventApp->topics_relationship_get_questions($topic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic),
                'total-questions-syllabus' => $topic->questions->count() + $total_questions_subtopics
            ]
        ]);
    }

    public function topic_relationship_questions_read($topic, $question)
    {
        \Log::debug('SchemaJson->topic_relationship_questions_read');

        return QuestionResource::make(
            $this->eventApp->topic_relationship_questions_read($topic, $question)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function topic_relationship_questions_create($request, $topic)
    {
        return QuestionResource::make(
            $this->eventApp->topic_relationship_questions_create($request, $topic)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function topic_relationship_questions_update($request, $topic, $question)
    {
        return QuestionResource::make(
            $this->eventApp->topic_relationship_questions_update($request, $topic, $question)
        )->additional([
            'meta' => [
                'topic' => TopicResource::make($topic)
            ]
        ]);
    }

    public function topic_relationship_questions_delete($topic, $question)
    {
        $this->eventApp->topic_relationship_questions_delete($topic, $question);

        return response()->noContent();
    }

    public function claim_question_mail($request)
    {
        $this->eventApp->claim_question_mail($request);

        return response()->json([
            'status' => 'successfully'
        ]);
    }

    public function import_records($request)
    {
        $this->eventApp->import_records( $request );

        return response()->json([
            'message' => "Proceso de importación iniciada"
        ], 200);
    }

    public function set_mode_edit_question($request, $question)
    {
        $this->eventApp->set_mode_edit_question($request, $question);

        return response()->json(['message' => 'successfully']);
    }
}
