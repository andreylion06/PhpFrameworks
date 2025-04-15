<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\TableReservation;
use App\Form\TableReservationType;
use App\Repository\TableReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/table-reservations')]
final class TableReservationController extends AbstractController{
    #[Route(name: 'app_table_reservation_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        $queryBuilder = $em->getRepository(TableReservation::class)->createQueryBuilder('r')
            ->leftJoin('r.client', 'c')
            ->addSelect('c');

        if ($clientId = $request->query->get('client_id')) {
            $queryBuilder->andWhere('c.id = :clientId')->setParameter('clientId', $clientId);
        }

        if ($tableNumber = $request->query->get('table_number')) {
            $queryBuilder->andWhere('r.tableNumber = :tableNumber')->setParameter('tableNumber', $tableNumber);
        }

        if ($reservationDate = $request->query->get('reservation_date')) {
            $queryBuilder->andWhere('DATE(r.reservationDate) = :reservationDate')
                ->setParameter('reservationDate', $reservationDate);
        }

        $itemsPerPage = $request->query->getInt('itemsPerPage', 10);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $itemsPerPage
        );

        $clients = $em->getRepository(Client::class)->findAll();

        return $this->render('table_reservation/index.html.twig', [
            'pagination' => $pagination,
            'clients' => $clients,
        ]);
    }

    #[Route('/new', name: 'app_table_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tableReservation = new TableReservation();
        $form = $this->createForm(TableReservationType::class, $tableReservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tableReservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_table_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('table_reservation/new.html.twig', [
            'table_reservation' => $tableReservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_table_reservation_show', methods: ['GET'])]
    public function show(TableReservation $tableReservation): Response
    {
        return $this->render('table_reservation/show.html.twig', [
            'table_reservation' => $tableReservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_table_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TableReservation $tableReservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TableReservationType::class, $tableReservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_table_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('table_reservation/edit.html.twig', [
            'table_reservation' => $tableReservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_table_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, TableReservation $tableReservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tableReservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tableReservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_table_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
