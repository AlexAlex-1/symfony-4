<?php

namespace App\Controller;
use App\Entity\Tickets;
use App\Form\SeacrhType;
use App\Entity\User;
use App\Entity\Projects;
use App\Entity\Tags;
use App\Form\editTicket;
use App\Entity\TicketsTags;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class TicketsController extends AbstractController
{
    /**
     * @Route("/projects/tickets/{id}", name="ticket_show")
    */

    public function show(Request $request,Tickets $tictets, $id)
    {
        $ticket = $this->getDoctrine()->getRepository(Tickets::class)->find($id);
        $tictag = $this->getDoctrine()->getRepository(TicketsTags::class)->findBy(['Ticket_id'=>$id]);
        $project = $this->getDoctrine()->getRepository(Projects::class)->findByTicket($id);
        $assignee = $this->getDoctrine()->getRepository(User::class)->findByTicket($id);
        $tagsID = array();
        $tags = $this->getDoctrine()->getRepository(Tags::class)->findByTickets($id);
            if (!isset($ticket)) {
                return $this->render('404.html.twig');
            }

            return $this->render('/tickets/show.html.twig', [
                'ticket'=>$ticket,
                'tags'=>$tags,
                'project'=>$project,
                'assignee'=>$assignee,
            ]);
    }

    /**
    * @Route("/projects/{id}/tickets/create", methods="GET|POST")
    */
    public function createTickets(Request $request, Projects $project, $id, SessionInterface $session) { 
        $ticket = new Tickets();
        $userId_session = $this->getUser()->getId();
        $userName_session = $this->getUser()->getUsername();
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $userName = array($userName_session=>$userId_session);

        foreach ($users as $user) {
            $userName[$user->getUsername()] = $user->getId();
        }

        $form = $this->createFormBuilder($ticket)
            ->add('Name')
            ->add('ProjectId', HiddenType::class, array('data'=>$id))
            ->add('UserId', HiddenType::class, array('data'=>$userId_session))
            ->add('Description',TextType::class)
            ->add('AssigneeId', ChoiceType::class, array('choices'=>$userName))
            ->add('Status', ChoiceType::class, array(
                'choices' => array(
                    'New'=>'New',
                    'In progress'=>'In progress',
                    'Testing'=>'Testing',
                    'Done'=>'Done'
                )
            ))
            ->add('File', FileType::class)
            ->add('Submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // $this->addFlash(
            //     'sms',
            //     'Проект сохранен!'
            // );
            $file = $ticket->getFile();
            $fileName = $this->uniqueName().'.'.$file->getClientOriginalExtension();

            try {
                $file->move(
                    $this->getParameter('file_directory'),
                    $fileName
                );
            } catch (FileException $e) { }

            $ticket->setFile($fileName);
            $ticket = $form->getData();
            $ticketid = $ticket->getId();
            $save = $this->getDoctrine()->getManager();
            $save->persist($ticket);
            $save->flush();
            return $this->redirectToRoute("projects_show", ['id'=>$id]);
        }
        return $this->render('/tickets/create.html.twig', array(
        'ticket'=>$ticket,
        'id'=>$id,
        'form'=>$form->createView(),
       ));
    }

    /**
    * @Route("/projects/tickets/del/{id}")
    */
    public function deleteTicket(Request $request, Tickets $tickets, $id)
    {
        $repository = $this->getDoctrine()->getManager();
        $ticket = $repository->getRepository(Tickets::class)->find($id);
        $repository->remove($ticket);
        $repository->flush();
        return $this->redirectToRoute('start');
    }

    /**
    * @Route("/projects/tickets/{id}/edit", name="ticket_edit", methods="GET|POST")
    */
    public function editTicket(Request $request, Tickets $tickets, $id)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $userNames = array();
        foreach ($users as $user){
            $userNames[$user->getUsername()] = $user->getId();
        }
        $form = $this->createFormBuilder($tickets)
            ->add('Name')
            ->add('Description')
            ->add('AssigneeId', ChoiceType::class, array('choices'=>$userNames))
            ->add('Status', ChoiceType::class, array(
                'choices'=>array(
                    'New'=>'New',
                    'In progress'=>'In progress',
                    'Testing'=>'Testing',
                    'Done'=>'Done',
                ),
            ))
            ->add('Tag', TextType::class, array('mapped'=>false))
            ->add('Submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tagName = $form->get('Tag')->getData();
            $tagsNames = explode(",", $tagName);
            $tagsNames = array_unique($tagsNames);
            // var_dump($tagsNames);
            
            foreach ($tagsNames as $key => $value) {
                $tag = $this->getDoctrine()->getRepository(Tags::class)->findBy(['Name'=>$value]);

                // if not found in DB
                if (!$tag) {
                    $newTag = new Tags();
                    $newTag->setName($tagsNames[$key]);

                    $save = $this->getDoctrine()->getManager();
                    $save->persist($newTag);
                    $save->flush();

                    $newTagId = $newTag->getId();
                    $ticTag = new TicketsTags();
                    $ticTag->setTicketId($id);
                    $ticTag->setTagId($newTagId);
                    $save = $this->getDoctrine()->getManager();
                    $save->persist($ticTag);
                    $save->flush();
                } else {
                    $newTagId = $tag[0]->getId();

                    // arr tags from current ticket
                    $tics = $this->getDoctrine()->getRepository(TicketsTags::class)->findBy(['Ticket_id'=>$id]);
                    // var_dump($tics);

                    // newTagId found?
                    $tagFound = false;
                    foreach ($tics as $key => $value) {
                        $tagId_ticket = $value->getTagId();
                        if ($newTagId == $tagId_ticket) {
                            $tagFound = true;
                            // $this->addFlash(
                            //     'Error_Tag',
                            //     'Такой тег у тикета существует!'
                            // );
                            // return $this->redirectToRoute("ticket_show", ['id'=>$id]);
                        } 
                    }
                    // if newTagId not found:
                    if (!$tagFound) {
                        $ticTag = new TicketsTags();
                        $ticTag->setTicketId($id);
                        $ticTag->setTagId($newTagId);
                        $save = $this->getDoctrine()->getManager();
                        $save->persist($ticTag);
                        $save->flush();
                    }
                }
            }
            $this->getDoctrine()->getManager()->flush();
            $form = $this->createForm(editTicket::class, $tickets);
            $form->handleRequest($request);
            return $this->redirectToRoute('ticket_show', ['id'=>$id]);
        }

        return $this->render('tickets/edit.html.twig', [
        'ticket'=>$tickets,
        'form'=>$form->createView(),
        ]);
    }
    private function uniqueName()
    {
        return md5(uniqid());
    }

    /**
    * @Route("/user/tickets", name="tickets_user")
    */


    public function ticketUser(Request $request){
        $user = $this->getUser();
        $id = $this->getUser()->getId();
        $ticket = $this->getDoctrine()->getRepository(Tickets::class)->findBy(['user_id'=>$id]);
        if(!isset($ticket)){
            return $this->render('404.html.twig');
        }
        return $this->render('/tickets/tickets_user.html.twig',[
        'user'=>$user,
        'ticket'=>$ticket,
        ]);
    }
    /**
     * @Route("/search", name="search")
     */

    public function search(Request $request){
        $form = $this->createForm(SeacrhType::class);
        $form->handleRequest($request);
        return $this->render("/tickets/test.html.twig",[
        'form'=>$form->createView(),  
        ]);
    }
    /**
     * @Route("/search/processing", name="processing")
     */
    public function processing(Request $request){
        $post = $request->get('seacrhName');
        $object = $this->getDoctrine()->getRepository(Tags::class)->findByString($post);
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($object, 'json');
        return new Response($jsonContent);
    }
}
