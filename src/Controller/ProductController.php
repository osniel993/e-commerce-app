<?php

namespace App\Controller;

use App\Services\ProductServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProductController extends AbstractController
{

    protected string $status = "success";

    public function __construct(
        protected ProductServices $productServices,
        protected RequestStack    $request
    )
    {
        $this->productServices->setParams($this->request->getCurrentRequest()->request->all());
    }

    #[Route('/product', name: 'app_product', methods: 'get')]
    public function list(): JsonResponse
    {
        try {
            $this->productServices->setParams($this->request->getCurrentRequest()->query->all());
            return new JsonResponse($this->productServices->find());
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 500);
        }
    }

    #[Route('/product/add', name: 'app_product_add', methods: 'post')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(): JsonResponse
    {
        try {
            $this->productServices->add();

            return new JsonResponse([
                'status' => $this->status,
                'msg' => 'Product add'
            ]);
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            $error = $th->getCode() == -1 ? json_decode($message) : $message;
            return new JsonResponse($error, 500);
        }
    }

    /**
     * @throws \Exception
     */
    #[Route('/product/edit', name: 'app_product_edit', methods: 'post')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(): JsonResponse
    {
        try {
            $this->productServices->edit();

            return new JsonResponse([
                'status' => $this->status,
                'msg' => 'Product edited'
            ]);
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            $error = $th->getCode() == -1 ? json_decode($message) : $message;
            return new JsonResponse($error, 500);
        }
    }

    #[Route('/product/remove', name: 'app_product_remove', methods: 'delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function remove(): JsonResponse
    {
        try {
            $this->productServices->remove();

            return new JsonResponse([
                'status' => $this->status,
                'msg' => 'Product removed'
            ]);
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 500);
        }
    }

    #[Route('/product/sell', name: 'app_product_sell', methods: 'post')]
    public function sell(): JsonResponse
    {
        try {
            $this->productServices->sell();

            return new JsonResponse([
                'status' => $this->status
            ]);
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 500);
        }
    }

    #[Route('/product/out-stock', name: 'app_product_outofstock', methods: 'post')]
    public function outofstock(): JsonResponse
    {
        try {
            $this->productServices->setParams($this->request->getCurrentRequest()->query->all());
            return new JsonResponse([
                $this->productServices->outOfStock()
            ]);
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 500);
        }
    }

    #[Route('/product/list-sold', name: 'app_product_productssold')]
    public function pullProductsSold(): JsonResponse
    {
        try {
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 500);
        }
        $this->productServices->setParams($this->request->getCurrentRequest()->query->all());
        return new JsonResponse([
            $this->productServices->pullProductsSold()
        ]);
    }

    #[Route('/product/total-profit', name: 'app_product_totalprofit')]
    public function pullTotalProfit(): JsonResponse
    {
        try {
            return new JsonResponse([
                'status' => $this->status,
                'TotalProfit' => $this->productServices->pullTotalProfit()
            ]);
        } catch (\Throwable $th) {
            return new JsonResponse($th->getMessage(), 500);
        }
    }
}
