<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Entity\Tickets;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class TagsController extends AbstractController
{

    /**
     * @Route("/tags/{id}", name="tags_search")
     */

    public function show(Tags $tag)
    {
        $tagId = $tag->getId();
        $tickets = $this->getDoctrine()
            ->getRepository(Tickets::class)
            ->findByTags($tagId);
        return $this->render('tags/show.html.twig', [
            'tickets' => $tickets,
            'tags' =>$tag,
        ]);
    }
}
