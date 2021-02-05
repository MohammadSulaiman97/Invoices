<?php

namespace App\Http\Controllers\API\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Invoice;
use App\Http\Resources\InvoiceAndDetails;
use App\Models\invoices;
use App\Models\invoices_attachments;
use App\Models\invoices_details;
use App\Models\sections;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\API\BaseController as BaseController;


class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function create()
    {
        $sections = sections::all();
        return $this->sendResponse(Invoice::collection($sections),
            'تم ارسال جميع الاقسام');
    }

    public function store(Request $request)
    {
        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'due_Date' => $request->due_Date,
            'product_id' => $request->product_id,
            'section_id' => $request->section_id,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);


        $invoice_id = invoices::latest()->first()->id;
        invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product_id' => $request->product_id,
            'section_id' => $request->section_id,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);


        $invoices = invoices::latest()->first();

        return $this->sendResponse(new InvoiceAndDetails($invoices) ,'تم اضافة الفاتورة بنجاح ' );

    }



    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json(['errors' => false, 'message' => 'Successfully logged out']);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json($user,200);
    }
}
