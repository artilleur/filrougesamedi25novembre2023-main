<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\CommandeDetail;
use App\Entity\Facture;
use App\Entity\Transporteur;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Produit as EntityProduit;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $generator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
    }

    #[Route('/order/create-session-stripe/{id}', name: 'payment_stripe',methods: ['POST'])]
    //il s'agit ici de l'id de la commande en cours qui nous sert aussi de référence
    public function stripeCheckout($id): RedirectResponse
    {
        $productStripe = [];
        //recupére la commande en cours
      $order = $this->em->getRepository(Commande::class)->findOneBy(['id' => $id]);
     //dd($order);
     //si commande introuvable ou n'existe pas
     if(!$order){
        return $this->redirectToRoute('panier_index');
     }
     $total= 0;
        $soustotal = 0;
        $fdp = 6;
        $totaltva = 0;
        $remise = 0;
        $total=0;
     foreach ($order->getCommandeDetails()->getValues() as $product) {
        //pour recup le nom du produit
        $productData = $this->em->getRepository(Produit::class)->findOneBy(['id' => $product->getPro()]);
        //dd($productData);
        //les info demandé par stripe
        $producStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $product->getPrix() * 100,
                'product_data' => [
                    'name' => $productData->getNom()
                ]
                ],
                'quantity' => $product->getQuantite()
            ];

        
        $soustotal +=  $product->getPrix() * $product->getQuantite();

     }

     
 if ($this->isGranted('ROLE_COMMERCE')) {
    $tva =20;
    $remise =5;
} 
 elseif ($this->isGranted('ROLE_ADMIN')) {
    $tva =20;
    $remise=10;
} 
else {
    $tva = 20;
}


$totaltva +=round( $soustotal+($soustotal*$tva/100)-($soustotal*$remise/100),2) * 100;

$producStripe[] = [
    'price_data' => [
        'currency' => 'eur',
        'unit_amount' => round(($soustotal*$tva/100)-($soustotal*$remise/100),2) * 100,
        'product_data' => [
            'name' => 'tva+remise'
        ]
    ],
    'quantity' => 1,
];
//    dd($totaltva);
     if($totaltva>10000) {
        $total =$totaltva+0; 
        $producStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => 0,
                    'product_data' => [
                        'name' => 'fdp'
                    ]
                    ],
                    'quantity' => 1,
                ];
    }
    else {

        $producStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $fdp*100,
                'product_data' => [
                    'name' => 'fdp'
                ]
                ],
                'quantity' => 1,
            ];
    
}
 

   
    Stripe::setApiKey('sk_test_51O9QyeJcB7aIs6zZrucNH3s5gBlnUgquUXkR0KmrRdBGd3lVjWdp2jRc1OzsLGoj5LA5y1DISPIdT8pADgrT0DKX00qdskr1kk');

    //header('Content-Type: application/json');

//$YOUR_DOMAIN = 'http://localhost:4242';

$checkout_session = \Stripe\Checkout\Session::create([
    'customer_email' => $this->getUser()->getEmail(),
    'payment_method_types' => ['card'],
    'line_items' => [[
        $producStripe
    
    ]],
    'mode' => 'payment',
    'success_url' => $this->generator->generate('payment_success', [
      'id' => $order->getId()
    ],UrlGeneratorInterface::ABSOLUTE_URL),
    'cancel_url' => $this->generator->generate('payment_error', [
      'id' => $order->getId()
    ],UrlGeneratorInterface::ABSOLUTE_URL),
    //'success_url' => $YOUR_DOMAIN . '/success.html',
   // 'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
  ]);
  
      // $order->setComStripeSessionId($checkout_session->id);
      // $this->em->flush();
      return new RedirectResponse($checkout_session->url);
  
  
      }
      

    #[Route('/order/success/{id}', name: 'payment_success')]
    public function StripeSuccess(EntityManagerInterface $em,$id): Response{
        // $order = $this->em->getRepository(Commande::class)->findOneBy(['id' => $id]);
        // $order2 = $this->em->getRepository(CommandeDetail::class)->findOneBy(['id' => $id]);
        // $order3 = $this->em->getRepository(EntityProduit::class)->findOneBy(['id' => $id]);
        // //$order->setComIsPaid(true);
        // $facture=new Facture();
        // $facture->setCliNom($this->getUser()->getNom());
        // $facture->setCliPrenom($this->getUser()->getPrenom());
        // $facture->setCliEmail($this->getUser()->getEmail());
        // $facture->setCliTelephone($this->getUser()->getTelephone());
        // $facture->setAdresseLivraison($order->getAdresse());
        // $facture->setAdresseFacturation($order->getAdresseFact());
        // $facture->setProduit($order3->getNom());
        // $facture->setPrix($order3->getPrix());
        // $facture->setQuantite($order2->getQuantite());
        // // $facture->setComId($order->getId());
        
        // // $facture->setComDetail($order2->getId());

        // $facture->setIdCommande($order->getId());
       
        //  $em->persist($order2);
        //  $em->persist($order3);
        // $em->persist($facture);
        // $em->flush();
        //return $this->render('order/succes.html.twig');
        $order = $em->getRepository(Commande::class)->findOneBy(['id' => $id]);

        // Fetch the order details
        $orderDetails = $em->getRepository(CommandeDetail::class)->findBy(['com' => $id]);

        foreach ($orderDetails as $orderDetail) {
            // Fetch the product for each order detail
            $product = $orderDetail->getPro();

            // Update the product stock
            $newStock = $product->getStock() - $orderDetail->getQuantite();
            $product->setStock($newStock);

            // Persist the product entity
            $em->persist($product);
        }

        // Commit the transaction
        
        $em->flush();
        
        return $this->render('commande/success.html.twig');
    }

    #[Route('/order/error/{id}', name: 'payment_error')]
    public function StripeError($id): Response{
        //return $this->render('order/error.html.twig');
        return $this->render('commande/error.html.twig');
    }


}