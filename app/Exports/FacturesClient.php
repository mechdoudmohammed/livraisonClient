<?php

namespace App\Exports;


use App\Models\Client;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
class FacturesClient implements FromQuery,WithHeadings,WithColumnWidths,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct()
    {

    }

    public function query()
    {
        return Client::query()
        ->selectRaw('clients.username,clients.ribBank,banques.nomBank as bankName,sum(factures.total_facture-factures.frais_livraison_facture) as total')
        ->join('factures', 'factures.id_client','clients.id')
        ->join('banques','banques.id','clients.id_bank')
        ->where('factures.statut_facture','NOTPAID')
        ->whereIn('factures.type_facture', ['client','clientManual'])
        ->groupBy('factures.id_client')
        ->orderBy('banques.nomBank', 'desc');
       

        

    }
    public function headings(): array
    {
        return ["Client","RIB","Bank", "Total",'Verser','Payer'];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 35,     
            'C' => 25,
            'D' => 25,
            // 'E' => 20,     
            // 'F' => 25,
            // 'G' => 25,
            // 'H' => 20,     
            // 'I' => 25,

        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]
        
        
        
        ],


        ];
    }






    
}
