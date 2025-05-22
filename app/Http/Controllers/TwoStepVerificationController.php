<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\TwoStepVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TwoStepVerificationController extends Controller
{
    public function __construct(protected TwoStepVerificationService $service)
    {
    }

    public function initiate(Request $request, Invoice $invoice): JsonResponse
    {

        if ($invoice->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->service->initiate($request->user(), $invoice);

        return response()->json(['message' => 'Verification code sent.']);
    }

    public function verify(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate(['code' => 'required|string']);


        if ($invoice->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $this->service->verify($request->user(), $invoice, $request->code);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['message' => 'Verification successful.']);
    }
}
