<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoicePaymentRequest;
use App\Models\Invoice;
use App\Services\PayInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    public function __construct(private readonly PayInvoiceService $payInvoiceService)
    {
    }

    public function __invoke(InvoicePaymentRequest $request, Invoice $invoice): JsonResponse
    {
        $this->payInvoiceService->handle($request->user(), $invoice, $request->code);

        return response()->json([
            'message' => 'Invoice paid successfully',
            'invoice_id' => $invoice->id,
            'paid_at' => $invoice->paid_at,
        ]);
    }
}
