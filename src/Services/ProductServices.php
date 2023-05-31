<?php

namespace App\Services;

use App\Entity\Product;
use App\Exception\ApiException;
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

    public function add()
    {
        $product = $this->getProduct();

        $this->repository->save($product, true);
    }

    public function edit()
    {
        $sku = $this->getParam('sku');
        $product = $this->findOneProductBy(['sku' => $sku]);
        $product = $this->getProduct($product);

        $this->repository->save($product, true);
    }

    public function remove()
    {
        $sku = $this->getParam('sku');
        $product = $this->findOneProductBy(['sku' => $sku]);

        $this->repository->remove($product, true);
    }

    private function getProduct(Product $product = new Product()): Product
    {
        $errorList = [];
        foreach ($this->params as $param => $value) {
            $key = $this->pullParam($param);
            try {
                $product->{$key}($value);
            } catch (\Throwable $throwable) {
                $errorList[] = "{$param} field not allowed.";
            }
        }

        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorList[] = "For attr {$error->getPropertyPath()}: {$error->getMessage()}";
            }
        }

        if (!empty($errors)) {
            $error = new ApiException("The product must comply with the following requirements");
            $error->setErrorList($errorList);
            throw $error;
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

    private function findOneProductBy(array $param): Product
    {
        return $this->repository->findOneBy($param) ??
            throw new ApiException('Product not listed in the catalog');
    }

    private function getParam(string $key)
    {
        return $this->params[$key] ??
            throw new ApiException("Must specify the {$key}.");
    }

    public function sell(): void
    {
        $sku = $this->getParam('sku');
        $product = $this->findOneProductBy(['sku' => $sku]);

        if ($product->getQuantityStock() == 0) {
            throw new ApiException("No products to sell");
        }

        $product->setQuantitySold($product->getQuantitySold() + 1);
        $product->setQuantityStock($product->getQuantityStock() - 1);

        $this->repository->save($product, true);
    }

    public function outOfStock(): array
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

    public function pullProductsSold(): array
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

    private function pullOffset(): void
    {
        $this->page = $this->getParam('page');
        unset($this->params['page']);

        $this->offset = (($this->page - 1) * 10);
    }
}