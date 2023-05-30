<?php

namespace App\Services;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ProductServices
{
    protected const DEFAULT_PULL_PARAM = 'set';

    protected array $params;
    protected int $page;
    protected int $offset;

    public function __construct(
        protected ProductRepository  $repository,
        protected ValidatorInterface $validator,
        protected Serializer         $serializer)
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
        $this->pullOffset();
        $productList = $this->repository->findBy($this->params, null, 10, $this->offset);

        return [
            'currentPageNumber' => $this->page,
            'numItemsPerPage' => count($productList),
            'totalCount' => $this->repository->count($this->params),
            'items' => json_decode($this->serializer->serializer($productList, 'json')),
        ];
    }

    /**
     * @throws \Exception
     */
    public function add()
    {
        $product = $this->getProduct();

        $this->repository->save($product, true);
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
                throw new \Exception("{$param} field not allowed.");
            }
        }

        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            throw new \Exception((string)$errors);
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
        $this->pullOffset();
        $productList = $this->repository->findBy(['quantity_stock' => 0], null, 10, $this->offset);

        return [
            'currentPageNumber' => $this->page,
            'numItemsPerPage' => count($productList),
            'totalCount' => $this->repository->count(['quantity_stock' => 0]),
            'items' => json_decode($this->serializer->serializer($productList, 'json')),
        ];
    }

    public function pullProductsSold()
    {
        $this->pullOffset();
        $productList = $this->repository->createQueryBuilder('p')
            ->andWhere('p.quantity_sold > 0')
            ->setFirstResult($this->offset)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $totalCountProductList = $this->repository->createQueryBuilder('p')
            ->select("count(p.sku) as totalCount")
            ->andWhere('p.quantity_sold > 0')
            ->getQuery()
            ->getResult();

        return [
            'currentPageNumber' => $this->page,
            'numItemsPerPage' => count($productList),
            'totalCount' => $totalCountProductList[0]['totalCount'],
            'items' => json_decode($this->serializer->serializer($productList, 'json')),
        ];
    }

    public function pullTotalProfit()
    {
        return $this->repository->pullTotalProfit()[0]['total_profit'];
    }

    private function pullOffset()
    {
        $this->page = $this->getParam('page');
        unset($this->params['page']);

        $this->offset = (($this->page - 1) * 10);
    }
}