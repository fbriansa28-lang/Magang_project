<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\LearningProgress;
use App\Models\LearningQuestionSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SimulasiController extends Controller implements HasMiddleware
{
    public static function middleware():array
    {
        return [
            new Middleware('permission:show learning', only: ['simulation', 'saveAnswer', 'riwayat', 'submitSimulation']),
        ];
    }
    public function simulation(Materi $learningMaterial)
    {
        abort_unless($learningMaterial->is_active, 404);

        $progress = LearningProgress::firstOrCreate(
            ['user_id' => auth()->id(), 'learning_material_id' => $learningMaterial->id],
            ['status' => 'in_progress']
        );

        // Simulasi yang sudah selesai tidak boleh dikerjakan ulang lewat halaman
        // ini -> arahkan ke halaman riwayat pengerjaan (mengikuti pola yang sama
        // dengan Challenge: attempt completed diarahkan ke leaderboard.participant).
        if ($progress->status === 'completed') {
            return redirect()->route('learning.riwayat', $learningMaterial);
        }

        $learningMaterial->load('questions.optionRows');

        // Ambil jawaban draft yang sudah pernah tersimpan (mis. peserta sempat
        // keluar dari simulasi), supaya pengerjaan lanjut dari posisi terakhir
        // dan tidak reset ke kosong lagi.
        $savedAnswers = $progress->submissions()
            ->get()
            ->keyBy('learning_question_id')
            ->map(fn ($submission) => $submission->submitted_answer);

        return view('learning.simulation', [
            'material' => $learningMaterial,
            'savedAnswers' => $savedAnswers,
        ]);
    }

    public function saveAnswer(Request $request, Materi $learningMaterial): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|integer|exists:learning_questions,id',
            'chosen' => 'nullable|string|max:2000',
        ]);

        $progress = LearningProgress::where('user_id', auth()->id())
            ->where('learning_material_id', $learningMaterial->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        // Simpan sebagai draft (belum dinilai benar/salah, penilaian final
        // tetap terjadi saat submitSimulation dipanggil).
        LearningQuestionSubmission::updateOrCreate(
            [
                'learning_progress_id' => $progress->id,
                'learning_question_id' => $validated['question_id'],
            ],
            [
                'submitted_answer' => $validated['chosen'] ?? '',
                'submitted_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function riwayat(Materi $learningMaterial)
    {
        abort_unless($learningMaterial->is_active, 404);

        $progress = LearningProgress::where('user_id', auth()->id())
            ->where('learning_material_id', $learningMaterial->id)
            ->where('status', 'completed')
            ->firstOrFail();

        $learningMaterial->load(['category', 'questions.optionRows']);

        $submissionByQuestion = $progress->submissions()->get()->keyBy('learning_question_id');

        return view('learning.riwayat', [
            'material' => $learningMaterial,
            'progress' => $progress,
            'submissionByQuestion' => $submissionByQuestion,
        ]);
    }

    public function submitSimulation(Request $request, Materi $learningMaterial): JsonResponse
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:learning_questions,id',
            'answers.*.chosen' => 'nullable|string|max:2000',
        ]);

        $questions = $learningMaterial->questions()->get()->keyBy('id');
        $totalQuestions = $questions->count();
        $correctCount = 0;

        $results = [];
        foreach ($validated['answers'] as $answer) {
            $question = $questions->get($answer['question_id']);
            $chosen = $answer['chosen'] ?? '';
            $isCorrect = $question ? $question->isCorrect($chosen) : false;
            if ($isCorrect) {
                $correctCount++;
            }
            $results[] = [
                'question_id' => $answer['question_id'],
                'chosen' => $chosen,
                'is_correct' => $isCorrect,
            ];
        }

        $progress = LearningProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'learning_material_id' => $learningMaterial->id],
            [
                'status' => 'completed',
                'correct_count' => $correctCount,
                'total_questions' => $totalQuestions,
                'completed_at' => now(),
            ]
        );

        // Jawaban per soal ditulis ulang setiap kali submit, supaya kalau
        // materi dikerjakan ulang (mis. setelah dibuka lagi lewat riwayat),
        // riwayat yang ditampilkan selalu mencerminkan pengerjaan terakhir.
        $progress->submissions()->delete();
        foreach ($results as $result) {
            LearningQuestionSubmission::create([
                'learning_progress_id' => $progress->id,
                'learning_question_id' => $result['question_id'],
                'submitted_answer' => $result['chosen'],
                'is_correct' => $result['is_correct'],
                'submitted_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'total_questions' => $totalQuestions,
            'correct_count' => $correctCount,
            'status' => $progress->status,
        ]);
    }
}