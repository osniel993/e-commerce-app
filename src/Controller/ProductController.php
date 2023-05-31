<?php

namespace App\Controller;

use App\Exception\ApiException;
use App\Services\ProductServices;
use mysql_xdevapi\Exception;
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
        $this->productServices->setParams($this->request->getCurrentRequest()->query->all());
        return new JsonResponse($this->productServices->find());
    }

    #[Route('/product/add', name: 'app_product_add', methods: 'post')]
    #[IsGranted('ROLE_ADMIN')]
    public function add(): JsonResponse
    {
        $this->productServices->add();

        return new JsonResponse([
            'status' => $this->status,
            'msg' => 'Product add'
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/product/edit', name: 'app_product_edit', methods: 'post')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(): JsonResponse
    {
        $this->productServices->edit();

        return new JsonResponse([
            'status' => $this->status,
            'msg' => 'Product edited'
        ]);
//        } catch (\Throwable $th) {
//            $message = $th->getMessage();
//            $error = $th->getCode() == -1 ? json_decode($message) : $message;
//            return new JsonResponse($error, 500);
//        }
    }

    #[Route('/product/remove', name: 'app_product_remove', methods: 'delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function remove(): JsonResponse
    {
        $this->productServices->remove();

        return new JsonResponse([
            'status' => $this->status,
            'msg' => 'Product removed'
        ]);
    }

    #[Route('/product/sell', name: 'app_product_sell', methods: 'post')]
    public function sell(): JsonResponse
    {
        $this->productServices->sell();

        return new JsonResponse([
            'status' => $this->status
        ]);
    }

    #[Route('/product/out-stock', name: 'app_product_outofstock', methods: 'post')]
    public function outofstock(): JsonResponse
    {
        $this->productServices->setParams($this->request->getCurrentRequest()->query->all());
        return new JsonResponse([
            $this->productServices->outOfStock()
        ]);
    }

    #[Route('/product/list-sold', name: 'app_product_productssold')]
    public function pullProductsSold(): JsonResponse
    {
        $this->productServices->setParams($this->request->getCurrentRequest()->query->all());
        return new JsonResponse([
            $this->productServices->pullProductsSold()
        ]);
    }

    #[Route('/product/total-profit', name: 'app_product_totalprofit')]
    public function pullTotalProfit(): JsonResponse
    {
        return new JsonResponse([
            'status' => $this->status,
            'TotalProfit' => $this->productServices->pullTotalProfit()
        ]);
    }
}
