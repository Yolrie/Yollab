<?php

namespace App\Controller;

use App\Entity\LineComment;
use App\Entity\Review;
use App\Entity\Snippet;
use App\Form\LineCommentType;
use App\Form\ReviewType;
use App\Form\SnippetType;
use App\Repository\SnippetRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/snippets')]
class SnippetController extends AbstractController
{
    // ── Step 4 : listing ────────────────────────────────────────────────────
    #[Route('', name: 'app_snippet_list')]
    public function list(Request $request, SnippetRepository $repo): Response
    {
        $tag      = $request->query->get('tag');
        $language = $request->query->get('language');
        $snippets = $repo->findPendingWithFilters($tag, $language);

        return $this->render('snippet/list.html.twig', [
            'snippets'  => $snippets,
            'languages' => Snippet::LANGUAGES,
            'tag'       => $tag,
            'language'  => $language,
        ]);
    }

    // ── Step 3 : submit ─────────────────────────────────────────────────────
    #[Route('/new', name: 'app_snippet_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em, TagRepository $tagRepo): Response
    {
        $snippet = new Snippet();
        $form    = $this->createForm(SnippetType::class, $snippet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $snippet->setAuthor($this->getUser());

            $rawTags = $form->get('tagsInput')->getData();
            if ($rawTags) {
                foreach (explode(',', $rawTags) as $name) {
                    $name = trim($name);
                    if ($name !== '') {
                        $snippet->addTag($tagRepo->findOrCreate($name));
                    }
                }
            }

            $em->persist($snippet);
            $em->flush();

            // Step 9 : +5 points pour la soumission
            $this->getUser()->addReputationPoints(5);
            $em->flush();

            $this->addFlash('success', 'Snippet soumis ! Il attend maintenant une review.');
            return $this->redirectToRoute('app_snippet_show', ['id' => $snippet->getId()]);
        }

        return $this->render('snippet/new.html.twig', ['form' => $form]);
    }

    // ── Step 5 & 6 : show + review + inline comments ────────────────────────
    #[Route('/{id}', name: 'app_snippet_show', requirements: ['id' => '\d+'])]
    public function show(
        Snippet $snippet,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $reviewForm      = null;
        $lineCommentForm = null;
        $user            = $this->getUser();

        if ($user && $snippet->getAuthor() !== $user) {
            $review     = new Review();
            $reviewForm = $this->createForm(ReviewType::class, $review);
            $reviewForm->handleRequest($request);

            if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
                $review->setSnippet($snippet)->setReviewer($user);
                $em->persist($review);

                // Marquer le snippet reviewed s'il a au moins 1 review
                $snippet->setStatus(Snippet::STATUS_REVIEWED);

                // Step 9 : +10 points pour le reviewer, +5 si approved pour l'auteur
                $user->addReputationPoints(10);
                if ($review->getStatus() === Review::STATUS_APPROVED) {
                    $snippet->getAuthor()->addReputationPoints(5);
                }

                $em->flush();
                $this->addFlash('success', 'Review soumise, merci !');
                return $this->redirectToRoute('app_snippet_show', ['id' => $snippet->getId()]);
            }

            $lineComment     = new LineComment();
            $lineCommentForm = $this->createForm(LineCommentType::class, $lineComment);
            $lineCommentForm->handleRequest($request);

            if ($lineCommentForm->isSubmitted() && $lineCommentForm->isValid()) {
                $lineComment->setSnippet($snippet)->setAuthor($user);
                $em->persist($lineComment);
                $user->addReputationPoints(2);
                $em->flush();
                $this->addFlash('success', 'Commentaire ajouté.');
                return $this->redirectToRoute('app_snippet_show', ['id' => $snippet->getId()]);
            }
        }

        // Grouper les commentaires inline par numéro de ligne
        $lineCommentsByLine = [];
        foreach ($snippet->getLineComments() as $lc) {
            $lineCommentsByLine[$lc->getLineNumber()][] = $lc;
        }
        ksort($lineCommentsByLine);

        return $this->render('snippet/show.html.twig', [
            'snippet'            => $snippet,
            'reviewForm'         => $reviewForm?->createView(),
            'lineCommentForm'    => $lineCommentForm?->createView(),
            'lineCommentsByLine' => $lineCommentsByLine,
        ]);
    }

    // ── Step 10 : JSON API ───────────────────────────────────────────────────
    #[Route('/api/by-tag/{tag}', name: 'app_api_snippets_by_tag')]
    public function apiByTag(string $tag, SnippetRepository $repo): JsonResponse
    {
        $snippets = $repo->findRecentByTag($tag);

        $data = array_map(fn(Snippet $s) => [
            'id'        => $s->getId(),
            'title'     => $s->getTitle(),
            'language'  => $s->getLanguage(),
            'status'    => $s->getStatus(),
            'author'    => $s->getAuthor()->getUsername(),
            'tags'      => $s->getTags()->map(fn($t) => $t->getName())->toArray(),
            'createdAt' => $s->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ], $snippets);

        return $this->json($data);
    }
}
