<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 *
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function myfacture($id): array
    {
        
        $qb = $this->createQueryBuilder('f')
            //->select('c.id as c_id, u.id as user_id, u. pro.id as p_id, produit.nom as p_nom, detail.prix as p_prix, detail.quantite as p_quantite')
            ->select('f.id as fact_id,c.id as com_id,cd.id as  c.adresse as adresse_livraison, c.adresse_fact as adresse_facturation,   produit.nom as p_nom, detail.prix as prix, detail.quantite as quantite, c.date_commande as date')
            ->join('c.utilisateur', 'u')
            ->join('commandeDetails', 'detail')
            ->join('detail.pro', 'produit')
            ->join('c.Commande','com')
            ->where('u.id = :comUtiId')
            ->setParameter('comUtiId', $id);
          
    
        $query = $qb->getQuery();
    
        return $query->execute();
        // return $query->getResult();
    
        // to get just one result:
        // $product = $query->setMaxResults(1)->getOneOrNullResult();
    }

//    /**
//     * @return Facture[] Returns an array of Facture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Facture
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
