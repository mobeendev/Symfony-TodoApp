<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\{Bundle\FrameworkExtraBundle\Configuration\ParamConverter};
use Knp\Component\Pager\PaginatorInterface;
class TodoController extends AbstractController
{
    /**
     * @Route("/todo", name="todo")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $emRepo = $this->getDoctrine()->getManager()
            ->getRepository(Task::class); // findALl first

        $allTasksQuery = $emRepo->createQueryBuilder('p')
            ->getQuery();

        // Paginate the results of the query
        $tasks = $paginator->paginate(
        // Doctrine Query, not results
            $allTasksQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5);

        // Render the twig view
        return $this->render('todo/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/create", name="create_task", methods = {"POST"})
     */

    public function create(Request $request)
    {
        if(empty ($title = trim($request->request->get('title'))) )
            return $this->redirectToRoute('todo');

        $request->request->get('title');
        $entityManager = $this->getDoctrine()->getManager();

        $task = new Task();
        $task->setTitle($title);

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('todo');
    }

    /**
     * @Route("/delete/{id}", name="task_delete")
     * @ParamConverter("task", class="App\Entity\Task")
     */
    public function delete(Task $task)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($task);
        $entityManager->flush();
        return $this->redirectToRoute('todo');
    }

    /**
     * @Route("/switch-status/{id}", name="switch_status")
     */
    public function switchStatus($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus(! $task->getStatus() );
        $entityManager->flush();
        return $this->redirectToRoute('todo');
    }

}
