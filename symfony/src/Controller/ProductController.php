<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/products')]
class ProductController extends AbstractController
{
    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $products = $em->getRepository(Product::class)->findAll();

        $data = [];
        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
            ];
        }

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('', name: 'app_product_new', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name'] ?? '');
        $product->setDescription($data['description'] ?? '');
        $product->setPrice($data['price'] ?? 0);

        $em->persist($product);
        $em->flush();

        return $this->json([
            'message' => 'Product created',
            'id' => $product->getId(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(string $id, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ], Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }

        $em->flush();

        return $this->json(['message' => 'Product updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function delete(string $id, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted'], Response::HTTP_OK);
    }
}
