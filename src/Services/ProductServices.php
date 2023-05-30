<?php

namespace App\Services;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RequestStack;


class ProductServices
{
    protected const DEFAULT_PULL_PARAM = 'set';

    protected array $params;

    public function __construct(
        protected ProductRepository $repository,
        protected Serializer        $serializer)
    {
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function find()
    {
//        $page = $this->request->getCurrentRequest()->get('page') ?? 1;
//        $productList = $this->repository->findAll();
//        $paginator = $this->paginator->paginate($productList, $page, 10);
//
//        return [
//            'currentPageNumber' => $paginator->getCurrentPageNumber(),
//            'numItemsPerPage' => $paginator->getItemNumberPerPage(),
//            'totalCount' => $paginator->getTotalItemCount(),
//            'items' => json_decode($this->serializer->serializer($paginator->getItems(), 'json')),
//        ];
    }

    /**
     * @throws \Exception
     */
    public function add()
    {
        $this->repository->save($this->getProduct(), true);
    }

    /**
     * @throws \Exception
     */
    public function edit()
    {
        $sku = $this->getParam('sku');
        $product = $this->findOneProductBy(['sku' => $sku]);
        $product = $this->getProduct($product);
        $this->repository->save($product, true);
    }

    /**
     * @throws \Exception
     */
    public function remove()
    {
        $sku = $this->getParam('sku');
        $product = $this->findOneProductBy(['sku' => $sku]);
        $this->repository->remove($product, true);
    }

    /**
     * @throws \Exception
     */
    private function getProduct(Product $product = new Product()): Product
    {
        foreach ($this->params as $param => $value) {
            $key = $this->pullParam($param);
            try {
                $product->{$key}($value);
            } catch (\Throwable $throwable) {
                throw new Exception("{$param} field not allowed.");
            }
        }

        return $product;
    }

    private function pullParam(string $param): string
    {
        $param = str_replace('_', ' ', $param);
        $param = ucwords($param);
        $param = str_replace(' ', '', $param);

        return self::DEFAULT_PULL_PARAM . $param;
    }

    /**
     * @throws \Exception
     */
    private function findOneProductBy(array $param)
    {
        return $this->repository->findOneBy($param) ??
            throw new \Exception('Product not listed in the catalog');
    }

    /**
     * @throws \Exception
     */
    private function getParam(string $key)
    {
        return $this->params[$key] ??
            throw new \Exception("Must specify the {$key}.");
    }

    /**
     * @throws \Exception
     */
    public function sell()
    {
        $sku = $this->getParam('sku');
        $product = $this->findOneProductBy(['sku' => $sku]);

        if ($product->getQuantityStock() == 0) {
            throw new \Exception("No hay produtos para vender");
        }

        $product->setQuantitySold($product->getQuantitySold() + 1);
        $product->setQuantityStock($product->getQuantityStock() - 1);

        $this->repository->save($product, true);
    }

    public function outOfStock()
    {
//        $page = $this->request->getCurrentRequest()->get('page') ?? 1;
//        $productsOfStock = $this->repository->findBy(['quantity_stock' => 0]);
//        $paginator = $this->paginator->paginate($productsOfStock, $page, 10);
//
//        return [
//            'currentPageNumber' => $paginator->getCurrentPageNumber(),
//            'numItemsPerPage' => $paginator->getItemNumberPerPage(),
//            'totalCount' => $paginator->getTotalItemCount(),
//            'items' => json_decode($this->serializer->serializer($paginator->getItems(), 'json')),
//        ];
    }

    public function pullProductsSold()
    {
//        $page = $this->request->getCurrentRequest()->get('page') ?? 1;
//        $productsSold = $this->repository->createQueryBuilder('p')
//            ->andWhere('p.quantity_sold > 0')
//            ->getQuery()
//            ->getResult();
//        $paginator = $this->paginator->paginate($productsSold, $page, 10);
//
//        return [
//            'currentPageNumber' => $paginator->getCurrentPageNumber(),
//            'numItemsPerPage' => $paginator->getItemNumberPerPage(),
//            'totalCount' => $paginator->getTotalItemCount(),
//            'items' => json_decode($this->serializer->serializer($paginator->getItems(), 'json')),
//        ];
    }

    public function pullTotalProfit()
    {
        return $this->repository->pullTotalProfit()[0]['total_profit'];
    }
}