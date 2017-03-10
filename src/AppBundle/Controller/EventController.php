<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Event;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
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

        $form = $this->getForm($event, false);
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
     * @param Event $event
     * @param bool $isNew
     * @return Form
     */
    protected function getForm($event, $isNew)
    {
        $operation = $isNew ? 'Create' : 'Update';
        $timezone = $this->getUser()->getTimezone();
        $tzCode = (new \DateTime())->setTimezone(new \DateTimeZone($timezone))->format('T');

        return $this->createFormBuilder($event)
            ->add('name', TextType::class, [
            	'attr' => [
            		'help' => "A useful, descriptive name",
	            ],
            ])
	        ->add('location', TextType::class, [
	        	'attr' => [
	        		'help' => "Where the sign-ups will be going",
		        ],
	        ])
            ->add('slots', IntegerType::class, [
            	'attr' => [
            		'help' => "How many you can take - will not prevent signups above this cap",
	            ],
            ])
	        ->add('start_time', DateTimeType::class, [
		        'date_widget' => 'single_text',
		        'time_widget' => 'single_text',
		        'view_timezone' => $timezone,
		        'attr' => [
			        'help' => "In your configured timezone ({$tzCode})",
		        ],
	        ])
            ->add('duration_interval', DateIntervalType::class, [
            	'label' => 'Duration',
	            'with_years' => false,
	            'with_months' => false,
	            'with_days' => false,
	            'with_hours' => true,
	            'with_minutes' => true,
	            'with_seconds' => false,
	            'widget' => 'integer'
            ])
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
        $future = $repo->createQueryBuilder('e')
            ->where('e.startTime > :startTime')
            ->setParameter('startTime', new \DateTime('today'))
            ->getQuery()->getResult();

	    $past = $repo->createQueryBuilder('e')
		    ->where('e.startTime < :startTime')
		    ->setParameter('startTime', new \DateTime('today'))
		    ->setMaxResults(10)
		    ->getQuery()->getResult();

        return $this->render('AppBundle:Event:index.html.twig', array(
            'future_events' => $future,
	        'past_events' => $past,
        ));
    }

    /**
     * @Route("/event/delete", name="event_delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Event:delete.html.twig', array(
            // ...
        ));
    }

	/**
	 * @Route("/event/read", name="event_read")
	 */
    public function readAction()
    {
	    return $this->render('AppBundle:Event:read.html.twig', array(
		    // ...
	    ));
    }
}
