<?php

namespace App\Controller;

use App\Entity\Manufacturer;
use App\Model\Part;
use App\Part\Finder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @var Finder
     */
    private $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => dirname($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/part/search/{number}", name="part-search")
     */
    public function searchPartAction(string $number)
    {
        $em = $this->getDoctrine()->getManager();
        $manufacturerRepository = $em->getRepository(Manufacturer::class);

        $parts = $this->finder->search($number);

        if (!$parts) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        return $this->json(array_map(function (Part $model) use ($em, $manufacturerRepository) {
            $manufacturer = $manufacturerRepository->findOneBy(['name' => $model->manufacturer]);
            if (!$manufacturer) {
                $manufacturer = new Manufacturer();
                $manufacturer->setName($model->manufacturer);
                $em->persist($manufacturer);
            }

            return [
                'manufacturer' => [
                    'id'   => $manufacturer->getId(),
                    'name' => $manufacturer->getName(),
                ],
                'name'         => $model->name,
                'number'       => $model->number,
            ];
        }, array_filter($parts, function (Part $model) use ($number) {
            return false !== strpos($model->number, $number);
        })));
    }
}
