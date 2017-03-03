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
     * @Route("/event/new", name="event_new")
     */
    public function newAction(Request $request)
    {
        $event = new Event();
        $event->setOrganizer($this->getUser());
        $event->setStartTime(new \DateTime('today'));
	    $event->setSlots(20);
	    $event->setDurationMinutes(3 * 60);

        $form = $this->getForm($event, true);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFormData($form, true);

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('AppBundle:Event:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/event/edit", name="event_edit")
     */
    public function editAction(Request $request)
    {
        $event = $this
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository('AppBundle:Event')
            ->findOneBy(['id' => $request->get('id')]);

        $form = $this->getForm($event, false);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFormData($form, false);

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('AppBundle:Event:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param Event $event
     * @param bool $isNew
     * @return Form
     */
    protected function getForm($event, $isNew)
    {
        $operation = $isNew ? 'Create' : 'Update';
        return $this->createFormBuilder($event)
            ->add('name', TextType::class)
            ->add('slots', IntegerType::class)
            ->add('duration_minutes', IntegerType::class, ['label' => 'Duration (in minutes)'])
            ->add('start_time', DateTimeType::class)
            ->add('save', SubmitType::class, ['label' => "$operation Event"])
            ->getForm();
    }

    /**
     * @param Form $form
     * @param bool $isNew
     */
    protected function saveFormData($form, $isNew)
    {
        $event = $form->getData();

        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->persist($event);
        $em->flush();

        $operation = $isNew ? 'created' : 'updated';
        $this->addFlash('success', "Event has been $operation!");
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
     * @Route("/event/delete", name="event_delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Character:delete.html.twig', array(
            // ...
        ));
    }

}
