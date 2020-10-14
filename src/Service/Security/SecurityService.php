<?php

declare(strict_types=1);

namespace App\Service\Security;

use App\Entity\User;
use App\Service\Mailer;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Twig\Environment;

/**
 * Class SecurityService
 */
class SecurityService
{
    private EntityManagerInterface $entityManager;
    private Mailer $mailer;
    private Environment $twig;
    private UserPasswordEncoderInterface $passwordEncoder;
    private MainAuthenticator $authenticator;
    private GuardAuthenticatorHandler $authenticatorHandler;
    private string $domain;
    private bool $useEmailConfirm;

    /**
     * @param Environment                  $twig
     * @param EntityManagerInterface       $entityManager
     * @param Mailer                       $mailer
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param MainAuthenticator            $authenticator
     * @param GuardAuthenticatorHandler    $authenticatorHandler
     * @param string                       $domain
     * @param bool                         $useEmailConfirm
     */
    public function __construct(
        Environment $twig,
        EntityManagerInterface $entityManager,
        Mailer $mailer,
        UserPasswordEncoderInterface $passwordEncoder,
        MainAuthenticator $authenticator,
        GuardAuthenticatorHandler $authenticatorHandler,
        string $domain,
        bool $useEmailConfirm
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->passwordEncoder = $passwordEncoder;
        $this->domain = $domain;
        $this->useEmailConfirm = $useEmailConfirm;
        $this->authenticatorHandler = $authenticatorHandler;
        $this->authenticator = $authenticator;
    }

    /**
     * @param User $user
     *
     * @throws Exception
     */
    public function register(User $user): void
    {
        if ($this->entityManager->contains($user)) {
            throw new Exception('User has been created later');
        }

        $user->setCreatedAt(new DateTime('now'));
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

        if ($this->useEmailConfirm) {
            $user->setConfirmEmail(substr(sha1(microtime()), 0, 16));
        }

        try {
            $this->entityManager->beginTransaction();
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->entityManager->commit();

            if ($this->useEmailConfirm) {
                $this->mailer->send(
                    $user->getEmail(),
                    'Confirm Email',
                    $this->twig->render('mail/confirm_email.html.twig', ['user' => $user, 'domain' => $this->domain])
                );
            }
        } catch (Exception $exception) {
            $this->entityManager->rollBack();

            throw $exception;
        }
    }

    /**
     * @param string|null $email
     * @param string|null $code
     *
     * @return bool
     */
    public function confirmEmail(?string $email, ?string $code): bool
    {
        if ($email === null || $code === null) {
            return false;
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user instanceof User || $user->getConfirmEmail() !== $code) {
            return false;
        }

        $user->setConfirmEmail(null);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return Response
     */
    public function login(Request $request, User $user): Response
    {
        return $this->authenticatorHandler->authenticateUserAndHandleSuccess(
            $user,
            $request,
            $this->authenticator,
            'main'
        );
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function getUser(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    /**
     * @return bool
     */
    public function requiredConfirmEmail(): bool
    {
        return $this->useEmailConfirm;
    }
}
