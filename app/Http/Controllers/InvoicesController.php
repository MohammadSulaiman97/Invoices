<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\invoices;
use App\Models\invoices_attachments;
use App\Models\invoices_details;
use App\Models\sections;
use App\Models\User;
use App\Notifications\AddInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;


class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = invoices::all();
        return view('invoices.invoices',compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add_invoice', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        invoices::create(
            [
                'invoice_number' => $request->invoice_number,
                'invoice_Date' => $request->invoice_Date,
                'due_Date' => $request->Due_date,
                'product_id' => $request->product_id,
                'section_id' => $request->section_id,
                'Amount_collection' => $request->Amount_collection,
                'Amount_Commission' => $request->Amount_Commission,
                'Discount' => $request->Discount,
                'Rate_VAT' => $request->Rate_VAT,
                'Value_VAT' => $request->Value_VAT,
                'Total' => $request->Total,
                'Status' => 'غير مدفوع',
                'Value_Status' => 2,
                'note' => $request->note,

            ]
        );

        $invoice_id = invoices::latest()->first()->id;

        invoices_details::create(
            [
                'id_Invoice' => $invoice_id,
                'invoice_number' =>$request->invoice_number,
                'product_id' => $request->product_id,
                'section_id' => $request->section_id,
                'Status' => 'غير مدفوع',
                'Value_Status' => 2,
                'note' => $request->note,
                'user' => (Auth::user()->name),
            ]
        );

        if($request->hasFile('pic')){
           /* $this->validate($request,[
                'pic' => 'required|mimes:pdf|max:10000'],[
                    'pic.mimes' => 'خطأ : تم حفظ الفاتورة ولم يتم حفظ المرفق لابد ان يكون pdf'
            ]);*/
            $invoice_id = invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;


            $attachments = new invoices_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = (Auth::user()->name);
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);

        }

        $user = User::first();
        Notification::send($user, new AddInvoice($invoice_id));

        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoices = invoices::where('id',$id)->first();
        return view('invoices.status_update',compact('invoices'));
    }

    public function Status_Update($id,Request $request){
        $invoices = invoices::findOrFail($id);
        if ($request->Status == 'مدفوعة'){
            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_details::create(
                [
                    'id_Invoice' => $request->invoice_id,
                    'invoice_number' =>$request->invoice_number,
                    'product_id' => $request->product_id,
                    'section_id' => $request->section_id,
                    'Value_Status' => 1,
                    'Status' => $request->Status,
                    'note' => $request->note,
                    'Payment_Date' => $request->Payment_Date,
                    'user' => (Auth::user()->name),
                ]
            );
        }
        else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            invoices_Details::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product_id' => $request->product_id,
                'section_id' => $request->section_id,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoices = invoices::where('id',$id)->first();
        $sections = sections::all();
        return view('invoices.edit_invoice',compact('invoices','sections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $invoices = invoices::findOrFail($request->invoice_id);
        $invoices->update([
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
            'note' => $request->note,
        ]);
        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return redirect('/invoices');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = invoices::where('id',$id)->first();
        $Details = invoices_attachments::where('invoice_id',$id)->first();

        $id_page = $request->id_page;

        if ($id_page == 2){

            if(!(empty($Details->invoice_number))){
                // Storage::disk('public_uploads')->delete($Details->invoice_number.'/'.$Details->file_name);
                Storage::disk('public_uploads')->deleteDirectory($Details->invoice_number);
            }

            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');
        }
        else{
            $invoices->Delete();
            session()->flash('archive_invoice');
            return redirect('/invoices');
        }


    }

    public function getproducts($id){
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

    public function Invoice_Paid()
    {
        $invoices = invoices::where('Value_Status', 1)->get();
        return view('invoices.invoices_paid',compact('invoices'));
    }

    public function Invoice_unPaid()
    {
        $invoices = invoices::where('Value_Status',2)->get();
        return view('invoices.invoices_unpaid',compact('invoices'));
    }

    public function Invoice_Partial()
    {
        $invoices = invoices::where('Value_Status',3)->get();
        return view('invoices.invoices_Partial',compact('invoices'));
    }

    public function Print_invoice($id)
    {
        $invoices = invoices::where('id', $id)->first();
        return view('invoices.Print_invoice',compact('invoices'));
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }
}
