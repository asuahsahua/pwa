<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{
    /**
     * @Route("/event/new")
     */
    public function newAction(Request $request)
    {
        $event = new Event();
        $event->setOrganizer($this->getUser());
        $event->setStartTime(new \DateTime('today'));
	    $event->setSlots(20);
	    $event->setDurationMinutes(3 * 60);

        /** @var Form $form */
        $form = $this->createFormBuilder($event)
	        ->add('name', TextType::class)
	        ->add('slots', IntegerType::class)
	        ->add('duration_minutes', IntegerType::class, ['label' => 'Duration (in minutes)'])
            ->add('start_time', DateTimeType::class)
	        ->add('save', SubmitType::class, ['label' => 'Save Event'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $character = $form->getData();

            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($character);
            $em->flush();

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('AppBundle:Event:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/event")
     */
    public function indexAction()
    {
        $repo = $this->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:Event');

        /** @var Event[] $events */
        $events = $repo->createQueryBuilder('e')
            ->where('e.startTime > :startTime')
            ->setParameter('startTime', new \DateTime('today'))
            ->getQuery()->getResult();

        return $this->render('AppBundle:Event:index.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * @Route("/event/delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Character:delete.html.twig', array(
            // ...
        ));
    }

}
