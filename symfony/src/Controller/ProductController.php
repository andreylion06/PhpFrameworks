<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/products')]
class ProductController extends AbstractController
{
    private const PRODUCTS = [];

    #[Route('/', name: 'get_products', methods: ['GET'])]
    public function getProducts(EntityManagerInterface $em): JsonResponse
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->json($products, Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'get_product_item', methods: ['GET'])]
    public function getProductItem(string $id, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], status: Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => $product], status: Response::HTTP_OK);
    }

    #[Route('/', name: 'post_products', methods: ['POST'])]
    public function createProduct(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $requestData = json_decode($request->getContent(), associative: true);

        $product = new Product();
        $product->setName($requestData['name']);
        $product->setDescription($requestData['description']);
        $product->setPrice($requestData['price']);

        $em->persist($product);
        $em->flush();

        return new JsonResponse(['data' => ['message' => 'Product created', 'id' => $product->getId()]], status: Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_product', methods: ['PUT'])]
    public function updateProduct(string $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], status: Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), associative: true);
        $product->setName($requestData['name'] ?? $product->getName());
        $product->setDescription($requestData['description'] ?? $product->getDescription());
        $product->setPrice($requestData['price'] ?? $product->getPrice());

        $em->flush();

        return new JsonResponse(['data' => ['message' => 'Product updated']], status: Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function deleteProduct(string $id, EntityManagerInterface $em): JsonResponse
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['data' => ['error' => 'Not found product by id ' . $id]], status: Response::HTTP_NOT_FOUND);
        }

        $em->remove($product);
        $em->flush();

        return new JsonResponse(['data' => ['message' => 'Product deleted']], status: Response::HTTP_OK);
    }
}

