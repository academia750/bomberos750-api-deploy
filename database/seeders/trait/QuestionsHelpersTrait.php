<?php
namespace Database\Seeders\trait;

trait QuestionsHelpersTrait
{
    public function registerQuestionsModel ($model, $fieldTextQuestion, $fakerText, $keyModel): void {
        foreach ( range(1, random_int(5,15)) as $number ) {
            $model->questions()->create([
                'question' => "{$keyModel} Question of {$fieldTextQuestion} - {$number}",
                'reason' => "Reason {$number} - {$fakerText}",
                'is_visible' => "yes",
                'its_for_test' => fake()->randomElement(['yes', 'no']),
                'its_for_card_memory' => fake()->randomElement(['yes', 'no']),
            ]);
        }

        $this->registerAnswersOfQuestion($model->questions);

    }

    public function registerAnswersOfQuestion ($questions): void
    {
        foreach ($questions as $question) {
            $thereIsAnswerGrouper = false;
            $thereIsAnswerCorrect = false;


            $answers = [
                [
                    'answer' => "Answer - 1 (Correct) Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'yes',
                    'question_id' => $question->getRouteKey(),
                ],
                [
                    'answer' => "Answer - 2 (Agrupadora) Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'yes',
                    'is_correct_answer' => 'no',
                    'question_id' => $question->getRouteKey(),
                ],
                [
                    'answer' => "Answer - 3 Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'no',
                    'question_id' => $question->getRouteKey(),
                ],
                [
                    'answer' => "Answer - 4 Q ({$question->getRouteKey()})",
                    'is_grouper_answer' => 'no',
                    'is_correct_answer' => 'no',
                    'question_id' => $question->getRouteKey(),
                ]
            ];

            foreach ($answers as $answer) {
                $question->answers()->create([
                    'answer' => $answer['answer'],
                    'is_grouper_answer' => $answer['is_grouper_answer'],
                    'is_correct_answer' => $answer['is_correct_answer']
                ]);
            }

        }
    }
}
