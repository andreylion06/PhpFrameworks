<?php

namespace App\Controller;

use App\Entity\MenuItem;
use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order-items')]
final class OrderItemController extends AbstractController
{
    #[Route(name: 'app_order_item_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $queryBuilder = $em->getRepository(OrderItem::class)->createQueryBuilder('oi')
            ->leftJoin('oi.orderRef', 'o')
            ->leftJoin('oi.menuItem', 'mi')
            ->addSelect('o', 'mi');

        if ($orderId = $request->query->get('order_id')) {
            $queryBuilder->andWhere('o.id = :orderId')->setParameter('orderId', $orderId);
        }

        if ($menuItemId = $request->query->get('menu_item_id')) {
            $queryBuilder->andWhere('mi.id = :menuItemId')->setParameter('menuItemId', $menuItemId);
        }

        if ($min = $request->query->get('quantity_min')) {
            $queryBuilder->andWhere('oi.quantity >= :min')->setParameter('min', $min);
        }

        if ($max = $request->query->get('quantity_max')) {
            $queryBuilder->andWhere('oi.quantity <= :max')->setParameter('max', $max);
        }

        $itemsPerPage = $request->query->getInt('itemsPerPage', 10);
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), $itemsPerPage);

        $results = [];
        foreach ($pagination as $item) {
            $results[] = [
                'id' => $item->getId(),
                'order_id' => $item->getOrderRef()->getId(),
                'menu_item' => [
                    'id' => $item->getMenuItem()->getId(),
                    'name' => $item->getMenuItem()->getName()
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        return $this->json([
            'data' => $results,
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'totalItems' => $pagination->getTotalItemCount(),
                'itemsPerPage' => $pagination->getItemNumberPerPage(),
            ]
        ]);
    }

    #[Route('/new', name: 'app_order_item_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $data = json_decode($request->getContent(), true);

        $order = $em->getRepository(Order::class)->find($data['order_id'] ?? null);
        $menuItem = $em->getRepository(MenuItem::class)->find($data['menu_item_id'] ?? null);

        if (!$order || !$menuItem) {
            return $this->json(['error' => 'Invalid order_id or menu_item_id'], Response::HTTP_BAD_REQUEST);
        }

        $item = new OrderItem();
        $item->setOrderRef($order);
        $item->setMenuItem($menuItem);
        $item->setQuantity($data['quantity'] ?? 1);

        $em->persist($item);
        $em->flush();

        return $this->json(['message' => 'Order item created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_order_item_show', methods: ['GET'])]
    public function show(OrderItem $orderItem): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->json([
            'id' => $orderItem->getId(),
            'order_id' => $orderItem->getOrderRef()->getId(),
            'menu_item' => [
                'id' => $orderItem->getMenuItem()->getId(),
                'name' => $orderItem->getMenuItem()->getName()
            ],
            'quantity' => $orderItem->getQuantity(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_order_item_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, OrderItem $orderItem, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['order_id'])) {
            $order = $em->getRepository(Order::class)->find($data['order_id']);
            if ($order) {
                $orderItem->setOrderRef($order);
            }
        }

        if (isset($data['menu_item_id'])) {
            $menuItem = $em->getRepository(MenuItem::class)->find($data['menu_item_id']);
            if ($menuItem) {
                $orderItem->setMenuItem($menuItem);
            }
        }

        if (isset($data['quantity'])) {
            $orderItem->setQuantity($data['quantity']);
        }

        $em->flush();

        return $this->json(['message' => 'Order item updated']);
    }

    #[Route('/{id}', name: 'app_order_item_delete', methods: ['DELETE'])]
    public function delete(OrderItem $orderItem, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $em->remove($orderItem);
        $em->flush();

        return $this->json(['message' => 'Order item deleted']);
    }
}
