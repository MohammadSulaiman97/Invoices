<?php

namespace App\Exports;

use App\Models\invoices;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoicesExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       // return invoices::all();
        return invoices::select('invoice_number', 'invoice_Date', 'due_Date','section_id', 'product_id', 'Amount_collection','Amount_Commission', 'Rate_VAT', 'Value_VAT','Total', 'Status', 'Payment_Date','note')->get();
    }

    public function headings(): array
    {
        return [
            'invoice_number', 'invoice_Date', 'due_Date','section_id', 'product_id', 'Amount_collection','Amount_Commission', 'Rate_VAT', 'Value_VAT','Total', 'Status', 'Payment_Date','note'
        ];
    }
}
