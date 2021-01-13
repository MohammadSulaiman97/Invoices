<?php

namespace App\Http\Controllers;

use App\Models\invoices;
use App\Models\invoices_attachments;
use App\Models\invoices_details;
use App\Models\products;
use App\Models\sections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $id_invoices = invoices::latest()->first()->id;

        invoices_details::create(
            [
                'id_Invoice' => $id_invoices,
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
            $id_invoices = invoices::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;


            $attachments = new invoices_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = (Auth::user()->name);
            $attachments->invoice_id = $id_invoices;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);

        }

        session()->flash('Add', 'تم اضافة الفاتورة بنجاح');
        return back();

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show(invoices $invoices)
    {
        //
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
    public function destroy(invoices $invoices)
    {
        //
    }

    public function getproducts($id){
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

}
