<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Form\PokemonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokemonController extends AbstractController
{
    #[Route('/pokemon', name: 'app_pokemon')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pokemon);
            $entityManager->flush();

            return $this->redirectToRoute('pokemon_list');
        }

        return $this->render('pokemon/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pokemon/list', name: 'pokemon_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $pokemons = $entityManager->getRepository(Pokemon::class)->findAll();

        return $this->render('pokemon/list.html.twig', [
            'pokemons' => $pokemons,
        ]);
    }

    #[Route('/pokemon/edit/{id}', name: 'pokemon_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $entityManager->getRepository(Pokemon::class)->find($id);

        if (!$pokemon) {
            throw $this->createNotFoundException('Ce Pokémon n\'existe pas');
        }

        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('pokemon_list');
        }

        return $this->render('pokemon/edit.html.twig', [
            'form' => $form->createView(),
            'pokemon' => $pokemon,
        ]);
    }

    #[Route('/pokemon/delete/{id}', name: 'pokemon_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $entityManager->getRepository(Pokemon::class)->find($id);

        if (!$pokemon) {
            throw $this->createNotFoundException('Ce Pokémon n\'existe pas');
        }

        $entityManager->remove($pokemon);
        $entityManager->flush();

        return $this->redirectToRoute('pokemon_list');
    }
}
