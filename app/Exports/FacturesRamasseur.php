<?php

namespace App\Exports;


use App\Models\Employe;
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
class FacturesRamasseur implements FromQuery,WithHeadings,WithColumnWidths,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct()
    {

    }

    public function query()
    {
        return Employe::query()
        ->selectRaw('employes.username,employes.ribBank,banques.nomBank,sum(factures.frais_livraison_facture) as total')
        ->join('factures', 'factures.id_employe','employes.id')
        ->join('banques','banques.id','employes.id_bank')
        ->where('factures.statut_facture','NOTPAID')
        ->whereIn('factures.type_facture', ['ramasseur','ramasseurManual'])
        ->groupBy('factures.id_employe')
        ->orderBy('banques.nomBank', 'desc');
       

        

    }
    public function headings(): array
    {
        return ["Ramasseur","RIB","Bank", "Total",'Verser','Payer'];
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
