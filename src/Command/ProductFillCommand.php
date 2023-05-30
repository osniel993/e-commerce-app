<?php

namespace App\Command;

use App\Entity\Product;
use App\Services\ProductServices;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:product-fill',
    description: 'Add a short description for your command',
)]
class ProductFillCommand extends Command
{
    protected string $description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    protected string $attachedImg = '["img\/00-789845_1.jpeg","img\/00-789845_2.jpeg","img\/00-789845_3.jpeg"]';
    protected array $productName = [
        'Long sleeve bodysuits Newborn',
        'Pairs of socks',
        'Caps',
        'P long sleeve bodysuits',
        'Sleveless long sleeve M',
        'Sleveless long sleeve Bodies G',
        'Bodies short sleeve RN',
        'Boxers Boots Short Sleeve P',
        'Pants RN',
        'P overalls',
        'M overalls',
        'G overalls',
    ];
    protected array $category = [
        'For girls',
        'For boys',
        'For babies',
        'For home',
        'For play',
    ];
    protected array $tag = [
        'Girls',
        'Boys',
        'Babies',
    ];

    public function __construct(
        protected ProductServices $productServices
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $productQuantity = $io->askQuestion(new Question("Quantity of products to be generated:", '100'));

        $coutnProductName = count($this->productName)-1;
        $countCategory = count($this->category)-1;
        $countTag = count($this->tag)-1;


        for ($i = 0; $i < $productQuantity; $i++) {
            $currentProductname = $this->productName[rand(0, $coutnProductName)] . " -{$i}-";
            $product = [
                "sku" => hash('crc32', $currentProductname),
                "name" => $currentProductname,
                "categories" => $this->category[rand(0, $countCategory)],
                "tags" => $this->category[rand(0, $countTag)],
                "price" => rand(100, 500),
                "quantity_stock" => rand(0, 100),
                "description" => $this->description,
                "more_info" => $this->description,
                "rating" => rand(0, 100),
                "attached_img" => $this->attachedImg,
                "quantity_sold" => rand(0, 100),
            ];
            $this->productServices->setParams($product);
            $this->productServices->add();
        }
        $io->success("You have {$productQuantity} new product.");

        return Command::SUCCESS;
    }
}
