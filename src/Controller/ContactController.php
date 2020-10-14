<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|UserInterface getUser()
 */
class ContactController extends AbstractController
{
    private ContactRepository $contactRepository;

    /**
     * @param ContactRepository $contactRepository
     */
    public function __construct(ContactRepository $contactRepository)
    {
        $this->contactRepository = $contactRepository;
    }

    /**
     * @Route("/contacts/public", name="app.contacts.public")
     *
     * @return Response
     */
    public function publicContacts(): Response
    {
        return $this->render('contact/public.html.twig', ['contacts' => $this->contactRepository->findAll()]);
    }

    /**
     * @Route("/contacts/favorite", name="app.contacts.private")
     *
     * @return Response
     */
    public function favoriteContacts(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('contact/private.html.twig', ['contacts' => $user->getFavoriteContacts()]);
    }

    /**
     * @Route("/contacts/{id}/subscribe", name="app.contacts.subscribe")
     * @ParamConverter(name="contact", class="App\Entity\Contact")
     *
     * @param Request                $request
     * @param Contact|null           $contact
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function subscribeContact(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()->hasFavoriteContact($contact)) {
            $this->getUser()->addFavoriteContact($contact);

            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer') ?? '/');
    }

    /**
     * @Route("/contacts/{id}/remove", name="app.contacts.remove")
     * @ParamConverter(name="contact", class="App\Entity\Contact")
     *
     * @param Request                $request
     * @param Contact|null           $contact
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function removeContact(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->hasFavoriteContact($contact)) {
            $this->getUser()->removeFavoriteContact($contact);

            $entityManager->flush();
        }

        return $this->redirect($request->headers->get('referer') ?? '/');
    }
}
