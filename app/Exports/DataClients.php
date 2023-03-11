<?php

namespace App\Exports;

use App\Models\Article;
use App\Models\Commande;
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

class DataClients implements FromQuery, WithHeadings, WithColumnWidths, WithStyles
{
    private $id;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function query()
    {
        return Commande::query()
            ->selectRaw('commandes.nom_client_commande,commandes.adresse_client_commande,commandes.telephone_client_commande,villes.nom_ville,count(commandes.id_commande)')
            ->join('villes', 'villes.id', 'commandes.id_ville')
            ->groupby('commandes.nom_client_commande')
            ->where('commandes.id_client', $this->id);
    }
    public function headings(): array
    {
        return ["Nom complet", "Adresse", "Telephone", 'Ville', 'Nombre de commande'];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 25,
            'D' => 25,
            'E' => 25,

        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => [
                'font' => ['bold' => true]



            ],


        ];
    }
}
