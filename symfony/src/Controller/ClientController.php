<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/clients')]
final class ClientController extends AbstractController{
    #[Route(name: 'app_client_index', methods: ['GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $queryBuilder = $em->getRepository(Client::class)->createQueryBuilder('c');

        if ($name = $request->query->get('name')) {
            $queryBuilder->andWhere('c.name LIKE :name')->setParameter('name', "%$name%");
        }

        if ($email = $request->query->get('email')) {
            $queryBuilder->andWhere('c.email LIKE :email')->setParameter('email', "%$email%");
        }

        if ($phone = $request->query->get('phone')) {
            $queryBuilder->andWhere('c.phone LIKE :phone')->setParameter('phone', "%$phone%");
        }

        $itemsPerPage = $request->query->getInt('itemsPerPage', 10);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $itemsPerPage
        );

        $data = [];
        foreach ($pagination as $client) {
            $data[] = [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'email' => $client->getEmail(),
                'phone' => $client->getPhone(),
            ];
        }

        return $this->json([
            'data' => $data,
            'pagination' => [
                'currentPage' => $pagination->getCurrentPageNumber(),
                'totalItems' => $pagination->getTotalItemCount(),
                'itemsPerPage' => $pagination->getItemNumberPerPage(),
            ]
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $client = new Client();
        $client->setName($data['name'] ?? '');
        $client->setEmail($data['email'] ?? '');
        $client->setPhone($data['phone'] ?? '');

        $entityManager->persist($client);
        $entityManager->flush();

        return $this->json(['message' => 'Client created'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->json([
            'id' => $client->getId(),
            'name' => $client->getName(),
            'email' => $client->getEmail(),
            'phone' => $client->getPhone(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $client->setName($data['name']);
        }
        if (isset($data['email'])) {
            $client->setEmail($data['email']);
        }
        if (isset($data['phone'])) {
            $client->setPhone($data['phone']);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Client updated']);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['DELETE'])]
    public function delete(Client $client, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entityManager->remove($client);
        $entityManager->flush();

        return $this->json(['message' => 'Client deleted']);
    }
}
