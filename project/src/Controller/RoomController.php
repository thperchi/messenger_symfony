<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Room;
use App\Form\MessageType;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/room")
 */
class RoomController extends AbstractController
{
    /**
     * @Route("/", name="room_index", methods={"GET"})
     */
    public function index(RoomRepository $roomRepository): Response
    {
        return $this->render('room/index.html.twig', [
            'rooms' => $roomRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="room_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $room->addUser($this->getUser());
            $entityManager->flush();

            return $this->redirectToRoute('room_index');
        }

        return $this->renderForm('room/new.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="room_show", methods={"GET", "POST"})
     */
    public function show(Room $room, EntityManagerInterface $entityManager, Request $request): Response
    {
        $messages = $room->getMessages();
        $mess = new Message();
        $form = $this->createForm(MessageType::class, $mess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mess);
            $mess->setUser($this->getUser());
            $mess->setRoom($room);
            $entityManager->flush();

            return $this->redirectToRoute('room_index');
        }
        return $this->render('room/show.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
            'mess' => $messages,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="room_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('room_index');
        }

        return $this->renderForm('room/edit.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="room_delete", methods={"POST"})
     */
    public function delete(Request $request, Room $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('room_index');
    }
}
