<?php

namespace App\Controller\Stripe;

use App\Entity\Order;
use App\Services\CartServices;
use App\Services\StockManagerServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeSuccessPaymentController extends AbstractController
{
    /**
     * @Route("/stripe-payment-success/{StripeCheckoutSessionId}", name="stripe_payment_success")
     */
    public function index(?Order $order, CartServices $cartServices,
    EntityManagerInterface $manager,
    StockManagerServices $stockManager): Response
    {
        if(!$order || $order->getuser() !== $this->getUser()){
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid()){
            //commande payée
            $order->setIsPaid(true);//par defaut c'est false
            //destockage
            $stockManager->deStock($order);
            $manager->flush();
            $cartServices->deleteCart();//commande payé on supprime le contenu du panier
            //un mail au client
        }

        return $this->render('stripe/stripe_success_payment/index.html.twig', [
            'controller_name' => 'StripeSuccessPaymentController',
            'order' => $order
        ]);
    }
}
