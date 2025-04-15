<?php

namespace App\Controller;

use App\Entity\MenuItem;
use App\Form\MenuItemType;
use App\Repository\MenuItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/menu-items')]
final class MenuItemController extends AbstractController{
    #[Route(name: 'app_menu_item_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ) {
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

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $itemsPerPage
        );

        return $this->render('menu_item/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_menu_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $menuItem = new MenuItem();
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menuItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu_item/new.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_item_show', methods: ['GET'])]
    public function show(MenuItem $menuItem): Response
    {
        return $this->render('menu_item/show.html.twig', [
            'menu_item' => $menuItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_menu_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MenuItem $menuItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu_item/edit.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_item_delete', methods: ['POST'])]
    public function delete(Request $request, MenuItem $menuItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menuItem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($menuItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
