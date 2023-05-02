<?php

namespace App\Exports;

use App\Models\Article;
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
class ArticleHistorique implements FromQuery,WithHeadings,WithColumnWidths,WithStyles
{
    private $id;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return Article::query()
        ->select('articles.nom_article','commandes.id_commande','detailscommandes.quantite_article',
        'nom_client_commande','adresse_client_commande','telephone_client_commande',
        'prix_commande','etat_commande'
        )
        ->join('detailscommandes','detailscommandes.id_article','=','articles.id_article')
        ->Join('commandes','detailscommandes.id_commande','=','commandes.id_commande')
        ->where('articles.id_article', $this->id)
        ->where('commandes.etat_commande', 'DELIVERED');
    }
    public function headings(): array
    {
        return ["Article","Commande", "Quantite Article",'Nom Client','Adresse','Telephone','Prix Commande','Etat'];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,     
            'C' => 25,
            'D' => 25,
            'E' => 20,     
            'F' => 25,
            'G' => 25,
            'H' => 20,     
            'I' => 25,

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
