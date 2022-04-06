<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/orders", name="orders")
     */
    public function index(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findBy(
            ['table_number' => 'table1']
        );
        return $this->render('order/index.html.twig', [
            'orders' => $order
        ]);
    }

    /**
     * @Route("/order/{id}", name="order")
     */
    public function order(Dish $dish): Response
    {
        $order = new Order();
        $order->setTableNumber("table1");
        $order->setName($dish->getName());
        $order->setOrderNumber($dish->getId());
        $order->setPrice($dish->getPrice());
        $order->setStatus("open");

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $this->addFlash('order', $order->getName(). ' has been added to the order');
        
        return $this->redirect($this->generateUrl('menu'));
    }

    /**
     * @Route("/status/{id},{status}", name="status")
     */
    public function status($id, $status): Response
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Order::class)->find($id);

        $order->setStatus($status);
        $em->flush();

        return $this->redirect($this->generateUrl('orders'));
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function remove($id, OrderRepository $orderRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $del = $orderRepository->find($id);
        $em->remove($del);
        $em->flush();

        return $this->redirect($this->generateUrl('orders'));
    }
}