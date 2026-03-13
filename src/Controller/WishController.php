<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/wishes', name: 'wish_')]
final class WishController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(WishRepository $wishRepository): Response
    {
        $wishes = $wishRepository->findBy(['isPublished' => true], ['dateCreated' => 'DESC']);

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes
        ]);

    }

    #[Route('/{id}', name: 'detail', requirements: ['id' => '[0-9]+'])]
    public function detail(int $id, WishRepository $wishRepository): Response
    {

        $wish = $wishRepository->find($id);

        return $this->render('wish/detail.html.twig', [
            'wish' => $wish
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '[0-9]+'])]
    public function delete(int                    $id,
                           WishRepository         $wishRepository,
                           EntityManagerInterface $entityManager): Response
    {
        $wish = $wishRepository->find($id);
        $entityManager->remove($wish);
        $entityManager->flush();

        return $this->redirectToRoute('wish_list');
    }


    #[Route('/create', name: 'create')]
    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function createOrUpdate(
        EntityManagerInterface $entityManager,
        WishRepository         $wishRepository,
        Request                $request, int $id = null): Response
    {
        $wish = new Wish();
        if ($id != null) {
            $wish = $wishRepository->find($id);
        }

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            //appel à des services

            $entityManager->persist($wish);
            $entityManager->flush();

            return $this->redirectToRoute('wish_detail', ['id' => $wish->getId()]);
        }

        return $this->render($id ? 'wish/update.html.twig' : 'wish/create.html.twig', [
            'wishForm' => $wishForm
        ]);
    }


}
