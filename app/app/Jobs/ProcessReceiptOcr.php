<?php

namespace App\Jobs;

use App\Models\Receipt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class ProcessReceiptOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $receiptId
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /** @var Receipt $receipt*/
        $receipt = Receipt::findOrFail($this->receiptId);
        $receipt->update([
            'status' => 'processing'
        ]);

        $imagePath = storage_path('app/public/' . $receipt->image_path);

        $process = new Process([
            'tesseract',
            $imagePath,
            'stdout',
            '-l',
            'hrv+eng',
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                'OCR failed: ' . $process->getErrorOutput()
            );
        }

        $ocrText = trim($process->getOutput());

        $receipt->update([
            'ocr_text_raw' => $ocrText,
            'status' => 'needs_review',
        ]);

    }
}
