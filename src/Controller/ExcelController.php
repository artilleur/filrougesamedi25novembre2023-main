<?php
namespace App\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandeRepository; // Importez les repositories nécessaires
use App\Repository\UtilisateurRepository;
use App\Repository\CommandeDetailRepository;
use App\Repository\ProduitRepository;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Protection;

class ExcelController extends AbstractController
{
    // Supposons que vous avez une autre fonction ici.
    // Assurez-vous qu'elle est correctement fermée avant d'en commencer une nouvelle.

    #[Route('/facture/excel/{facId}', name: 'facture_excel')]
    public function generateExcel(int $facId, CommandeRepository $commandeRepo, UtilisateurRepository $clientRepo, ProduitRepository $produitrepo): Response
    {
        // Récupérer la commande et les informations de la facture
        $commande = $commandeRepo->find($facId); // Remplacez par votre méthode pour obtenir la commande
        $client = $clientRepo->find($commande->getUtilisateur()); // Récupérer les infos du client

        // Si aucune commande ou client n'est trouvé
        if (!$commande || !$client) {
            throw $this->createNotFoundException('Commande ou client non trouvé');
        }

        // Créez votre classe Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Masquer le quadrillage
$sheet->setShowGridlines(false);
        
        // Ajouter le logo
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath('/Users/bureau/Desktop/filrougesamedi25novembre2023-main/public/images/image.png'); // Remplacez par le chemin correct vers votre logo
        $drawing->setHeight(250); // Ajustez la taille
        $drawing->setCoordinates('A1'); // Place l'image dans la cellule A1
        //$drawing->setOffsetX(300);
        $drawing->setWorksheet($spreadsheet->getActiveSheet());
        
        // Ajouter les informations de la facture
        $sheet->setCellValue('A14', 'Facture N° CA80-' . $facId);
        $sheet->setCellValue('A15', 'Commande N° ' . $commande->getId() . ' du ' . $commande->getDateCommande()->format('Y-m-d'));
        $sheet->setCellValue('A16', 'Date : ' . date('Y-m-d'));
        
        
        // Ajouter les informations du client
$sheet->setCellValue('A17', 'Client:' .$client->getNom() . ' ' . $client->getPrenom());
//$sheet->setCellValue('B17', $client->getNom() . ' ' . $client->getPrenom());
$sheet->setCellValue('A18', 'Adresse : ' . $client->getAdresse());
$sheet->setCellValue('A20', 'Tel : ' . $client->getTelephone());
$sheet->setCellValue('A19', 'Email : ' . $client->getEmail());

$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];

// Appliquer les bordures aux cellules des informations de la facture et du client
$sheet->getStyle('A14:A20')->applyFromArray($styleArray);
        
        // Ajouter les détails de la commande (les colonnes du tableau)
        $sheet->fromArray(
            ['Ref ','nom produit', 'prix HT', 'Quantité', 'Total HT', 'TVA', 'Remise', 'Prix TTC'],
            null,
            'A25'
        );
        
        // Remplir les détails de la commande
        $rowIndex = 26;
        $finalTotal = 0;
        $startRow = 26;
        
        foreach ($commande->getCommandeDetails() as $commandeDetail) {
            $nomProduit = $commandeDetail->getPro()->getNom();
            $prixProduit = $commandeDetail->getPrix();
            $quantite = $commandeDetail->getQuantite();
        
            $totalHT = $prixProduit * $quantite;
            $tva = 20;
            $remise = 10;
        
            $prixTTC = ($totalHT * $tva / 100) + $totalHT - ($totalHT * $remise / 100);
        
            $sheet->fromArray(
                [
                    $commandeDetail->getId(),
                    $nomProduit,
                    $prixProduit,
                    $quantite,
                    $totalHT,
                    $tva . '%',
                    $remise . '%',
                    round($prixTTC, 2)
                ],
                null,
                'A' . $rowIndex
            );
        
            $finalTotal += $prixTTC;
            $rowIndex++;
        }
        
        // Ajouter la cellule "Total TTC"
        $totalRow = $rowIndex;
        $sheet->setCellValue('G' . $totalRow, 'Total TTC :');
        $sheet->setCellValue('H' . $totalRow, '=SUM(H' . $startRow . ':H' . ($rowIndex - 1) . ')');
        $sheet->getStyle('G' . $totalRow)->getFont()->setBold(true);
        $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('##0.00,"€"#');
        
        // Ajouter les bordures pour la partie des détails de la commande
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];

// Appliquer les bordures à toutes les cellules remplies
//$sheet->getStyle('A14:A20' . ($rowIndex ))->applyFromArray($styleArray);
$sheet->getStyle('A25:H' . ($rowIndex - 1))->applyFromArray($styleArray);

// Ajouter les bordures à la section des informations de la facture et du client
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];

// Appliquer les bordures aux cellules des informations de la facture et du client
$sheet->getStyle('A14:A20')->applyFromArray($styleArray);


// Ajouter les bordures à la ligne Total TTC également
$sheet->getStyle('G' . $totalRow . ':H' . $totalRow)->applyFromArray($styleArray);


        // Ajustement automatique des colonnes
        foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getStyle('A1:H'.$sheet->getHighestRow())->getAlignment()->setWrapText(true);

foreach ($sheet->getRowIterator() as $row) {
    $sheet->getRowDimension($row->getRowIndex())->setRowHeight(-1);
}
        // Activer la protection de la feuille avec un mot de passe (facultatif)
        $sheet->getProtection()->setPassword('4193'); // Vous pouvez définir un mot de passe ici
        $sheet->getProtection()->setSheet(true); // Active la protection de la feuille

        // Vous pouvez également autoriser certaines actions sur la feuille même si elle est protégée
        $sheet->getProtection()->setInsertRows(true); // Permet l'insertion de lignes
        $sheet->getProtection()->setFormatCells(true); // Permet le formatage des cellules
        
        // Créer le fichier Excel
        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });
        
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment;filename="facture.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }
}