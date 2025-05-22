<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\PayInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class InvoicePaymentController extends Controller
{
    public function __construct(private readonly PayInvoiceService $payInvoiceService)
    {
    }

    public function __invoke(Request $request, Invoice $invoice): JsonResponse
    {
        $this->payInvoiceService->handle($request->user(), $invoice);

        return response()->json([
            'message' => 'Invoice paid successfully',
            'invoice_id' => $invoice->id,
            'paid_at' => $invoice->paid_at,
        ]);
    }
}
