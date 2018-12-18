<?php
namespace App\Controller;
use App\Entity\Tickets;
use App\Entity\Projects;
use App\Form\editProject;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
class ProjectsController extends AbstractController
{
    /**
     * @Route("/projects", name="start")
     */
    public function index()
    {
        $projects = $this->getDoctrine()->getRepository(Projects::class)->findAll();
        return $this->render('projects/index.html.twig', [
            'controller_name' => 'ProjectsController',
            'projects' => $projects,
        ]);
    }
    /**
    * @Route("/projects/show/{id}", name="projects_show")
    */
    public function showProject($id){
        $project = $this->getDoctrine()->getRepository(Projects::class)->find($id);
        $tickets = $this->getDoctrine()->getRepository(Tickets::class)->findBy(['project_id'=>$id]);
        if (!isset($project)){
            return $this->render('/404.html.twig');
        }
        return $this->render('projects/show.html.twig',[
        'project'=>$project,
        'tickets'=>$tickets,
        ]);
    }
    /**
    * @Route("/projects/create", name="create", methods="GET|POST")
    */
    public function createProject(Request $request)
    {
        $project = new Projects();
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser()->getId();
        $form = $this->createFormBuilder($project)
            ->add('UserId', HiddenType::class, array('data'=>$user))
            ->add('Name')
            ->add('Submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
          $save = $this->getDoctrine()->getManager();
          $save->persist($project);
          $save->flush();
          $projectid = $project->getId();
          return $this->redirectToRoute("projects_show", ['id'=>$projectid]);
        }
        return $this->render('projects/create.html.twig', [
            'user'=>$user,
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }
    /**
    * @Route("/projects/edit/{id}", name="project_edit", methods="GET|POST");
    */
    public function editProject(Request $request, Projects $projects, $id){
      $this->denyAccessUnlessGranted('EDIT', $projects);
      $form = $this->createForm(editProject::class, $projects);
      $form->handleRequest($request);
      if($form->isSubmitted() && $form->isValid()){
        $this->getDoctrine()->getManager()->flush();
        $form = $this->createForm(editProject::class, $projects);
        $form->handleRequest($request);
        return $this->redirectToRoute('projects_show', ['id'=>$id]);
      }
      return $this->render('projects/edit.html.twig', [
        'project'=>$projects,
        'form'=>$form->createView(),
      ]);
    }
    /**
    * @Route("/projects/del/id={id}",methods="GET|POST");
    */
    public function deleteProject(Request $request, Projects $projects, $id){
        $repository = $this->getDoctrine()->getManager();
        $project = $repository->getRepository(Projects::class)->find($id);
        $repository->remove($project);
        $repository->flush();
        return $this->redirectToRoute('start');
    }
    
    /**
    * @Route("/user/projects", name="projects_user")
    */
    
    public function userProjects(){
        $user = $this->getUser();
        $id = $this->getUser()->getId();
        $projects = $this->getDoctrine()->getRepository(Projects::class)->findBy(['user_id'=>$id]);
        return $this->render('projects/projects_user.html.twig', [
        'controller_name' => 'ProjectsController',
        'projects' => $projects,
        'user' => $user,
        ]);
    }
}
