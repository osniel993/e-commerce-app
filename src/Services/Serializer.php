<?php

namespace App\Services;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as ISerializer;

class Serializer
{
    protected $encoders;
    protected $normalizers;
    protected $serializer;

    public function __construct()
    {
        $this->encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];
        $this->serializer = new ISerializer($this->normalizers, $this->encoders);
    }


    public function serializer($object, string $format)
    {
        return $this->serializer->serialize($object, $format);
    }

}