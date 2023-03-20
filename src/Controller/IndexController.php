<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{
    Request,
    Response,
    JsonResponse
};
use App\Entity\Fruit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\{
    Serializer,
    Encoder\JsonEncoder,
    Normalizer\ObjectNormalizer
};

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response
    {
        return $this->render('list.html.twig');
    }

    #[Route('/favorites', name: 'app_favorites')]
    public function favorites(
        EntityManagerInterface $entityManager
    ): Response
    {

        // $queryAvgScore = $entityManager->getRepository(Fruit::class)->createQueryBuilder('g')
        //     ->select("sum(g.nutritions.fat)")
        //     ->where('g.favorite = 1')
        //     ->getQuery();
        // $avgScore = $queryAvgScore->getResult();
        return $this->render('favorites.html.twig');
    }

    #[Route('/api/favorite', name: 'favorite')]
    public function favorite(
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $id = $request->request->get('id', 0);
        $type = $request->request->get('type', 0);
        
        $fruit = $entityManager->getRepository(Fruit::class)->find($id);
        if (!$fruit) {
            return new JsonResponse([
                "success" => 0,
                'message' => 'No product found for id '.$id
            ]);
        } else {
            if ($type == 1) {
                $fruits = $entityManager->getRepository(Fruit::class)->findBy(
                    ['favorite' => 1]
                );
                if (count($fruits) >= 10) {
                    return new JsonResponse([
                        "success" => 0,
                        'message' => 'Favorites count reached to Max(10)'
                    ]);
                }
            }
            $fruit->setFavorite($type);
            $entityManager->flush();
            return new JsonResponse([
                "success" => 1,
                'message' => $type == 1 ? 'Successfully added' : 'Successfully removed'
            ]);    
        }
    }

    #[Route('/api/list', name: 'api_list_fruit')]
    public function list(
        EntityManagerInterface $entityManager
    ): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->entityManager = $entityManager;
        $fruits = $entityManager->getRepository(Fruit::class)->findAll();
        $serializer = new Serializer($normalizers, $encoders);
        $fruits = $serializer->serialize($fruits, 'json');
        return new JsonResponse([
            'data' => json_decode($fruits)
        ]);
    }

    #[Route('/api/list/favorites', name: 'api_list_favorites')]
    public function listFavorites(
        EntityManagerInterface $entityManager
    ): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->entityManager = $entityManager;
        $fruits = $entityManager->getRepository(Fruit::class)->findBy(
            ['favorite' => 1]
        );
        $serializer = new Serializer($normalizers, $encoders);
        $fruits = $serializer->serialize($fruits, 'json');
        return new JsonResponse([
            'data' => json_decode($fruits)
        ]);
    }
}
