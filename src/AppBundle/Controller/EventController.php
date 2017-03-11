<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use AppBundle\Form\EventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        $form = $this->createForm(EventType::class, $event, [
            'timezone' => $this->getUser()->getTimezone(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFormData($form, true);

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('AppBundle:Event:update.html.twig', array(
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

        $form = $this->createForm(EventType::class, $event, [
            'timezone' => $this->getUser()->getTimezone(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveFormData($form, false);

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('AppBundle:Event:update.html.twig', array(
            'form' => $form->createView(),
        ));
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
        $future = $repo->createQueryBuilder('e')
            ->where('e.startTime > :startTime')
            ->setParameter('startTime', new \DateTime('today'))
            ->orderBy('e.startTime', 'ASC')
            ->getQuery()->getResult();

        $past = $repo->createQueryBuilder('e')
            ->where('e.startTime < :startTime')
            ->setParameter('startTime', new \DateTime('today'))
            ->orderBy('e.startTime', 'DESC')
            ->setMaxResults(10)
            ->getQuery()->getResult();

        return $this->render('AppBundle:Event:index.html.twig', array(
            'future_events' => $future,
            'past_events'   => $past,
        ));
    }

    /**
     * @Route("/event/delete", name="event_delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Event:delete.html.twig', array(// ...
        ));
    }

    /**
     * @Route("/event/read", name="event_read")
     */
    public function readAction()
    {
        return $this->render('AppBundle:Event:read.html.twig', array(// ...
        ));
    }
}
