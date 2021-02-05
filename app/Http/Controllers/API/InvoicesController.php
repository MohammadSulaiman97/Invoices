<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\invoices;
use App\Models\invoices_attachments;
use App\Models\invoices_details;
use App\Models\sections;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\Invoice as InvoiceResource;
use App\Http\Controllers\API\BaseController as BaseController;

class InvoicesController extends BaseController
{

    public function index()
    {
        $invoices = invoices::all();
        return $this->sendResponse(InvoiceResource::collection($invoices),
          'تم ارسال جميع الفواتير');
    }

    public function show($id)
    {
        $invoices = invoices::find($id);
        
        if ( is_null($invoices) ) {
            return $this->sendError('الفاتورة غير موجودة');
              }
              return $this->sendResponse(new InvoiceResource($invoices) ,'تم عرض الفاتورة بنجاح' );
    }
    
    public function update(Request $request,$id)
    {
        $invoices = invoices::find($id);
           $invoices->update([
               'product_id' => $request->product_id,
               'section_id' => $request->section_id,
               'note' => $request->note,
        ]);
     
     $invoices->save();
     return $this->sendResponse(new InvoiceResource($invoices) ,'تم تعديل الفاتورة بنجاح' );

    }


    public function create()
    {
        $sections = sections::all();
        return $this->sendResponse(InvoiceResource::collection($sections),
            'تم ارسال جميع الاقسام');
    }

    public function store(Request $request)
    {
        $invoices = invoices::create([
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

        return $this->sendResponse(new InvoiceResource($invoices) ,'تم اضافة الفاتورة بنجاح ' );

    }

}