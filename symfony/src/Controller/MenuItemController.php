<?php

namespace App\Controller;

use App\Entity\MenuItem;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/menu-items')]
final class MenuItemController extends AbstractController
{
    #[Route(name: 'app_menu_item_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $queryBuilder = $em->getRepository(MenuItem::class)->createQueryBuilder('m');

        if ($name = $request->query->get('name')) {
            $queryBuilder->andWhere('m.name LIKE :name')->setParameter('name', "%$name%");
        }

        if ($min = $request->query->get('price_min')) {
            $queryBuilder->andWhere('m.price >= :min')->setParameter('min', $min);
        }

        if ($max = $request->query->get('price_max')) {
            $queryBuilder->andWhere('m.price <= :max')->setParameter('max', $max);
        }

        $itemsPerPage = $request->query->getInt('itemsPerPage', 10);
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), $itemsPerPage);

        $items = [];
        foreach ($pagination as $item) {
            $items[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'price' => $item->getPrice(),
            ];
        }

        return $this->json([
            'data' => $items,
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'totalItems' => $pagination->getTotalItemCount(),
                'itemsPerPage' => $pagination->getItemNumberPerPage(),
            ]
        ]);
    }

    #[Route('/new', name: 'app_menu_item_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $data = json_decode($request->getContent(), true);

        $item = new MenuItem();
        $item->setName($data['name'] ?? '');
        $item->setDescription($data['description'] ?? '');
        $item->setPrice($data['price'] ?? 0);

        $em->persist($item);
        $em->flush();

        return $this->json(['message' => 'Menu item created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_menu_item_show', methods: ['GET'])]
    public function show(MenuItem $menuItem): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->json([
            'id' => $menuItem->getId(),
            'name' => $menuItem->getName(),
            'description' => $menuItem->getDescription(),
            'price' => $menuItem->getPrice(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_menu_item_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, MenuItem $menuItem, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $menuItem->setName($data['name']);
        }

        if (isset($data['description'])) {
            $menuItem->setDescription($data['description']);
        }

        if (isset($data['price'])) {
            $menuItem->setPrice($data['price']);
        }

        $em->flush();

        return $this->json(['message' => 'Menu item updated']);
    }

    #[Route('/{id}', name: 'app_menu_item_delete', methods: ['DELETE'])]
    public function delete(MenuItem $menuItem, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $em->remove($menuItem);
        $em->flush();

        return $this->json(['message' => 'Menu item deleted']);
    }
}
