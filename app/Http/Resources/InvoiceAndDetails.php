<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Auth;

class InvoiceAndDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=> $this->id,
            'invoice_number'=> $this->invoice_number,
            'invoice_Date'=> $this->invoice_Date,
            'due_Date'=> $this->due_Date,
            'product_id'=> $this->product_id,
            'section_id'=> $this->section_id,
            'Amount_collection'=> $this->Amount_collection,
            'Amount_Commission'=> $this->Amount_Commission,
            'Discount'=> $this->Discount,
            'Value_VAT'=> $this->Value_VAT,
            'Rate_VAT'=> $this->Rate_VAT,
            'Total'=> $this->Total,
            'Status'=> $this->Status,
            'Value_Status'=> $this->Value_Status,
            'note'=> $this->note,
            'created_at'=> $this->created_at->format('d/m/Y'),
            'updated_at'=> $this->updated_at->format('d/m/Y'),
            'invoice_num'=> $this->invoice_number,
            'product'=> $this->product_id,
            'section'=> $this->section_id,
            'Statu'=> $this->Status,
            'Value_Statu'=> $this->Value_Status,
            'not'=> $this->note,
            'user' => (Auth::user()->name),
        ];
    }
}
