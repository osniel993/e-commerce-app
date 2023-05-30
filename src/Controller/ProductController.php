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
    public function __construct(
        protected ProductServices $productServices,
        protected RequestStack    $request
    )
    {
        $this->productServices->setParams($this->request->getCurrentRequest()->request->all());
    }

    #[Route('/product', name: 'app_product', methods: 'get')]
    public function index(): JsonResponse
    {
        return new JsonResponse($this->productServices->find());
    }

    #[Route('/product/add', name: 'app_product_add', methods: 'post')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(): JsonResponse
    {
        $status = 'success';
        $msg = 'Product add';

        $this->productServices->add();

        return new JsonResponse([
            'status' => $status,
            'msg' => $msg
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/product/edit', name: 'app_product_edit', methods: 'post')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(): JsonResponse
    {
        $status = 'success';
        $msg = 'Product edited';

        $this->productServices->edit();

        return new JsonResponse([
            'status' => $status,
            'msg' => $msg
        ]);
    }

    #[Route('/product/remove', name: 'app_product_remove', methods: 'delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function remove(): JsonResponse
    {
        $status = 'success';
        $msg = 'Product eliminado';

        $this->productServices->remove();

        return new JsonResponse([
            'status' => $status,
            'msg' => $msg
        ]);
    }

    #[Route('/product/sell', name: 'app_product_sell', methods: 'post')]
    public function sell(): JsonResponse
    {
        $status = 'success';
        $msg = 'Product vendido';

        $this->productServices->sell();

        return new JsonResponse([
            'status' => $status,
            'msg' => $msg
        ]);
    }

    #[Route('/product/outofstock', name: 'app_product_outofstock', methods: 'post')]
    public function outofstock(): JsonResponse
    {
        return new JsonResponse([
            $this->productServices->outOfStock()
        ]);
    }

    #[Route('/product/productssold', name: 'app_product_productssold')]
    public function pullProductsSold(): JsonResponse
    {
        return new JsonResponse([
            $this->productServices->pullProductsSold()
        ]);
    }

    #[Route('/product/totalprofit', name: 'app_product_totalprofit')]
    public function pullTotalProfit(): JsonResponse
    {
        $status = 'success';

        return new JsonResponse([
            'status' => $status,
            'TotalProfit' => $this->productServices->pullTotalProfit()
        ]);
    }
}
