<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/orders')]
final class OrderController extends AbstractController
{
    #[Route(name: 'app_order_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $queryBuilder = $em->getRepository(Order::class)->createQueryBuilder('o')
            ->leftJoin('o.client', 'c')
            ->addSelect('c');

        if ($clientId = $request->query->get('client_id')) {
            $queryBuilder->andWhere('c.id = :client')->setParameter('client', $clientId);
        }

        if ($createdAt = $request->query->get('created_at')) {
            $queryBuilder->andWhere('DATE(o.createdAt) = :createdAt')
                ->setParameter('createdAt', $createdAt);
        }

        $itemsPerPage = $request->query->getInt('itemsPerPage', 10);
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), $itemsPerPage);

        $orders = [];
        foreach ($pagination as $order) {
            $orders[] = [
                'id' => $order->getId(),
                'client' => [
                    'id' => $order->getClient()->getId(),
                    'name' => $order->getClient()->getName(),
                ],
                'createdAt' => $order->getCreatedAt()?->format('Y-m-d H:i'),
            ];
        }

        return $this->json([
            'data' => $orders,
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'totalItems' => $pagination->getTotalItemCount(),
                'itemsPerPage' => $pagination->getItemNumberPerPage(),
            ]
        ]);
    }

    #[Route('/new', name: 'app_order_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);
        $client = $em->getRepository(Client::class)->find($data['client_id'] ?? null);

        if (!$client) {
            return $this->json(['error' => 'Invalid client_id'], Response::HTTP_BAD_REQUEST);
        }

        $order = new Order();
        $order->setClient($client);
        $order->setCreatedAt(new \DateTime());

        $em->persist($order);
        $em->flush();

        return $this->json(['message' => 'Order created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_order_show', methods: ['GET'])]
    public function show(Order $order): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->json([
            'id' => $order->getId(),
            'client' => [
                'id' => $order->getClient()->getId(),
                'name' => $order->getClient()->getName(),
            ],
            'createdAt' => $order->getCreatedAt()?->format('Y-m-d H:i'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Order $order, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $data = json_decode($request->getContent(), true);
        if (isset($data['client_id'])) {
            $client = $em->getRepository(Client::class)->find($data['client_id']);
            if ($client) {
                $order->setClient($client);
            }
        }

        $em->flush();

        return $this->json(['message' => 'Order updated']);
    }

    #[Route('/{id}', name: 'app_order_delete', methods: ['DELETE'])]
    public function delete(Order $order, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($order);
        $em->flush();

        return $this->json(['message' => 'Order deleted']);
    }
}
