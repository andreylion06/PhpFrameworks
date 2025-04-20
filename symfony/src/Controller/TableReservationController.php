<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\TableReservation;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/table-reservations')]
final class TableReservationController extends AbstractController
{
    #[Route(name: 'app_table_reservation_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

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
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), $itemsPerPage);

        $reservations = [];
        foreach ($pagination as $r) {
            $reservations[] = [
                'id' => $r->getId(),
                'client' => [
                    'id' => $r->getClient()->getId(),
                    'name' => $r->getClient()->getName(),
                ],
                'tableNumber' => $r->getTableNumber(),
                'reservationDate' => $r->getReservationDate()->format('Y-m-d H:i'),
            ];
        }

        return $this->json([
            'data' => $reservations,
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'totalItems' => $pagination->getTotalItemCount(),
                'itemsPerPage' => $pagination->getItemNumberPerPage(),
            ]
        ]);
    }

    #[Route('/new', name: 'app_table_reservation_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');

        $data = json_decode($request->getContent(), true);
        $client = $entityManager->getRepository(Client::class)->find($data['client_id'] ?? null);

        if (!$client) {
            return $this->json(['error' => 'Invalid client_id'], Response::HTTP_BAD_REQUEST);
        }

        $reservation = new TableReservation();
        $reservation->setClient($client);
        $reservation->setTableNumber($data['table_number'] ?? 0);
        $reservation->setReservationDate(new \DateTime($data['reservation_date'] ?? 'now'));

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->json(['message' => 'Reservation created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_table_reservation_show', methods: ['GET'])]
    public function show(TableReservation $tableReservation): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->json([
            'id' => $tableReservation->getId(),
            'client' => [
                'id' => $tableReservation->getClient()->getId(),
                'name' => $tableReservation->getClient()->getName(),
            ],
            'tableNumber' => $tableReservation->getTableNumber(),
            'reservationDate' => $tableReservation->getReservationDate()->format('Y-m-d H:i'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_table_reservation_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, TableReservation $tableReservation, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['client_id'])) {
            $client = $entityManager->getRepository(Client::class)->find($data['client_id']);
            if ($client) {
                $tableReservation->setClient($client);
            }
        }

        if (isset($data['table_number'])) {
            $tableReservation->setTableNumber($data['table_number']);
        }

        if (isset($data['reservation_date'])) {
            $tableReservation->setReservationDate(new \DateTime($data['reservation_date']));
        }

        $entityManager->flush();

        return $this->json(['message' => 'Reservation updated']);
    }

    #[Route('/{id}', name: 'app_table_reservation_delete', methods: ['DELETE'])]
    public function delete(TableReservation $tableReservation, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $entityManager->remove($tableReservation);
        $entityManager->flush();

        return $this->json(['message' => 'Reservation deleted']);
    }
}
